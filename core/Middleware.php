<?php

namespace Core;

class Middleware
{
    public static function resolve($key)
    {
        if (strpos($key, 'role:') === 0) {
            $role = explode(':', $key)[1];
            self::checkRole($role);
            return;
        }

        switch ($key) {
            case 'auth':
                if (!Auth::check()) {
                    header('Location: /login');
                    exit;
                }
                break;
            case 'guest':
                if (Auth::check()) {
                    header('Location: /');
                    exit;
                }
                break;
            case 'profile.completed':
                if (Auth::check() && Auth::user()->profile_completed != 1) {
                    header('Location: /profile/complete');
                    exit;
                }
                break;
        }
    }

    protected static function checkRole($role)
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        // We assume spatie roles structure was used: users have a role assigned through model_has_roles
        // Let's query it. Spatie uses model_has_roles and roles table.
        // Wait, the Laravel app might have just added a 'role' column on the user, or used spatie.
        // Let's assume Spatie is used since there's permission tables.
        
        $userId = Auth::id();
        $stmt = Database::query("
            SELECT r.name 
            FROM roles r 
            JOIN model_has_roles mhr ON r.id = mhr.role_id 
            WHERE mhr.model_id = ? AND mhr.model_type = 'App\\\\Models\\\\User'
        ", [$userId]);
        
        $userRoles = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $hasRole = false;
        foreach ($userRoles as $r) {
            if ($r['name'] === $role) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            http_response_code(403);
            die("Unauthorized access.");
        }
    }
}
