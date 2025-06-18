<?php

class AuthMiddleware
{
    public static function isAuthenticated()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public static function requiredLogin()
    {
        if (!self::isAuthenticated()) {
            redirect('user/login');
            exit();
        }
    }
}
