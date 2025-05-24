<?php

// BASE_PATH and autoloader should handle these if classes are in the 'classes' directory
// require_once BASE_PATH . '/contact/db.php'; // For Database::getInstance()
// require_once BASE_PATH . '/classes/CookieConsentRepository.php';

class CookieConsentController {
    private CookieConsentRepository $cookieConsentRepo;

    public function __construct() {
        // It's assumed SessionManager::getInstance() in public/index.php has started the session.
        // And BASE_PATH is defined.
        if (!defined('BASE_PATH')) {
            // Fallback if not defined, though it should be by public/index.php
            define('BASE_PATH', dirname(__DIR__));
        }
        
        // Ensure Database class is available (usually via autoloader or direct require in db.php)
        // and CookieConsentRepository is also available.
        // The autoloader in public/index.php should handle CookieConsentRepository.
        // db.php needs to be required if Database class is not autoloadable.
        $dbPath = BASE_PATH . '/contact/db.php';
        if (file_exists($dbPath) && !class_exists('Database')) {
            require_once $dbPath;
        }
        
        $pdo = Database::getInstance();
        $this->cookieConsentRepo = new CookieConsentRepository($pdo);
    }

    /**
     * Handles the request to save user's cookie consent.
     * Outputs JSON response.
     */
    public function saveConsent(): void {
        header('Content-Type: application/json');
        $response = ['success' => false, 'message' => 'An error occurred.'];

        // Session should already be started by SessionManager via public/index.php
        if (!isset($_SESSION['user_id'])) {
            $response['message'] = 'User not authenticated.';
            echo json_encode($response);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response['message'] = 'Invalid request method.';
            echo json_encode($response);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $consentStatus = $input['consent_status'] ?? null;

        if (!$consentStatus || !in_array($consentStatus, ['accepted', 'rejected'])) {
            $response['message'] = 'Invalid consent status provided.';
            echo json_encode($response);
            exit;
        }

        $userId = (int)$_SESSION['user_id'];

        try {
            if ($this->cookieConsentRepo->saveConsent($userId, $consentStatus)) {
                $response['success'] = true;
                $response['message'] = 'Consent status updated successfully.';
                $_SESSION['user_cookie_consent_status'] = $consentStatus; // Update session immediately
            } else {
                $response['message'] = 'Failed to save consent status in the database.';
            }
        } catch (Exception $e) {
            // Log error $e->getMessage() for debugging
            error_log("CookieConsentController Error: " . $e->getMessage());
            $response['message'] = 'A server error occurred while saving consent.'; // Generic error for client
        }

        echo json_encode($response);
        exit;
    }
}