<?php

namespace Core;

class Controller
{
    protected function view($view, $data = [])
    {
        extract($data);
        $viewPath = BASE_PATH . "/views/{$view}.php";
        
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            die("View [{$view}] not found.");
        }
    }

    protected function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }
}
