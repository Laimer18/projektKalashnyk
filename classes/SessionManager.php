<?php

class SessionManager {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login($user): void {
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['email'] = $user->getEmail();
    }

    public static function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    public static function logout(): void {
        session_start();
        session_destroy();
    }

    public static function getUserId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }
}
