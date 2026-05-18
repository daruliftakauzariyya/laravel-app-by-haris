<?php

namespace Core;

class Router
{
    private $routes = [];
    private $middlewareMap = [];

    public function __construct()
    {
    }

    public function get($uri, $controller, $method = 'index')
    {
        return $this->add('GET', $uri, $controller, $method);
    }

    public function post($uri, $controller, $method)
    {
        return $this->add('POST', $uri, $controller, $method);
    }

    public function put($uri, $controller, $method)
    {
        return $this->add('PUT', $uri, $controller, $method);
    }

    public function delete($uri, $controller, $method)
    {
        return $this->add('DELETE', $uri, $controller, $method);
    }

    private function add($method, $uri, $controller, $action)
    {
        // Convert route parameters {id} to regex ([^/]+)
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[^/]+)', $uri);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'pattern' => $pattern,
            'controller' => $controller,
            'action' => $action,
            'middleware' => []
        ];

        return $this;
    }

    public function middleware($middleware)
    {
        if (is_array($middleware)) {
            $this->routes[array_key_last($this->routes)]['middleware'] = array_merge(
                $this->routes[array_key_last($this->routes)]['middleware'],
                $middleware
            );
        } else {
            $this->routes[array_key_last($this->routes)]['middleware'][] = $middleware;
        }
        return $this;
    }

    public function group($attributes, $callback)
    {
        $prefix = isset($attributes['prefix']) ? $attributes['prefix'] : '';
        $middleware = isset($attributes['middleware']) ? $attributes['middleware'] : [];
        if (!is_array($middleware)) {
            $middleware = [$middleware];
        }

        $oldRoutesLength = count($this->routes);
        
        $callback($this);

        for ($i = $oldRoutesLength; $i < count($this->routes); $i++) {
            $this->routes[$i]['uri'] = $prefix . $this->routes[$i]['uri'];
            
            // Recompute pattern
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[^/]+)', $this->routes[$i]['uri']);
            $this->routes[$i]['pattern'] = '#^' . $pattern . '$#';

            $this->routes[$i]['middleware'] = array_merge($middleware, $this->routes[$i]['middleware']);
        }
    }

    public function dispatch($uri, $method)
    {
        // Remove query string
        $uri = parse_url($uri, PHP_URL_PATH);
        
        // Handle method spoofing for PUT/DELETE
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                
                // Process middleware
                foreach ($route['middleware'] as $mw) {
                    \Core\Middleware::resolve($mw);
                }

                // Filter matches to only get named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                $controller = "App\\Controllers\\" . $route['controller'];
                $action = $route['action'];

                if (class_exists($controller)) {
                    $controllerInstance = new $controller();
                    if (method_exists($controllerInstance, $action)) {
                        return call_user_func_array([$controllerInstance, $action], $params);
                    }
                }
                
                die("Controller {$controller} or Action {$action} not found.");
            }
        }

        // 404
        http_response_code(404);
        echo "404 Not Found";
    }
}
