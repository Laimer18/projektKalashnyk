<?php

// It's good practice to place this in the same directory as other classes,
// or follow your project's autoloading structure if you have one.
// Assuming db.php is accessible from this path.
// If db.php is in the root 'contact' directory and this class is in 'classes',
// the path should be '../contact/db.php'.
// However, Database::getInstance() is usually available globally once included.
// require_once __DIR__ . '/../contact/db.php'; // May not be needed if Database class is autoloaded or already included

class CookieConsentRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Saves or updates a user's cookie consent.
     * If a record exists for the user_id, it updates the answer and date.
     * Otherwise, it inserts a new record.
     *
     * @param int $userId The ID of the user.
     * @param string $answer The consent answer ('accepted' or 'rejected').
     * @return bool True on success, false on failure.
     */
    public function saveConsent(int $userId, string $answer): bool
    {
        if (!in_array($answer, ['accepted', 'rejected'])) {
            // Invalid answer, perhaps log this or throw an exception
            return false;
        }

        $currentDate = date('Y-m-d H:i:s'); // Format for DATETIME or TIMESTAMP SQL type

        // Check if a record already exists for this user
        $stmtCheck = $this->pdo->prepare("SELECT user_id FROM cookie_consents WHERE user_id = :user_id LIMIT 1");
        $stmtCheck->execute([':user_id' => $userId]);
        
        if ($stmtCheck->fetch()) {
            // Update existing record
            $stmt = $this->pdo->prepare("
                UPDATE cookie_consents
                SET answer = :answer, date = :date
                WHERE user_id = :user_id
            ");
        } else {
            // Insert new record
            $stmt = $this->pdo->prepare("
                INSERT INTO cookie_consents (user_id, answer, date)
                VALUES (:user_id, :answer, :date)
            ");
        }

        return $stmt->execute([
            ':user_id' => $userId,
            ':answer'  => $answer,
            ':date'    => $currentDate,
        ]);
    }

    /**
     * Retrieves the latest cookie consent status for a given user.
     *
     * @param int $userId The ID of the user.
     * @return string|null The consent status ('accepted', 'rejected') or null if no record found.
     */
    public function getUserConsentStatus(int $userId): ?string
    {
        $stmt = $this->pdo->prepare("
            SELECT answer 
            FROM cookie_consents 
            WHERE user_id = :user_id 
            ORDER BY date DESC 
            LIMIT 1
        ");
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['answer'] : null; // If no record, consent is effectively pending
    }
}