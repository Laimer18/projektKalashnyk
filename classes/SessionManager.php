<?php

class SessionManager {
    public static function logout(): void {
        session_start();
        session_unset();
        session_destroy();
    }
}