<?php

namespace Core;

class Auth
{
    public static function check()
    {
        return isset($_SESSION['user_id']);
    }

    public static function user()
    {
        if (self::check()) {
            if (!isset($_SESSION['user_data'])) {
                $stmt = Database::query("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
                $_SESSION['user_data'] = $stmt->fetch();
            }
            return $_SESSION['user_data'];
        }
        return null;
    }

    public static function id()
    {
        return self::check() ? $_SESSION['user_id'] : null;
    }

    public static function login($user)
    {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_data'] = $user;
    }

    public static function logout()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_data']);
        session_destroy();
    }
}
