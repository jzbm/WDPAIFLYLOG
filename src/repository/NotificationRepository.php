<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../models/Notification.php';

class NotificationRepository {
    private $database;

    public function __construct() {
        $this->database = Database::getInstance()->connect();
    }

    public function getDatabase() {
        return $this->database;
    }

    // ✅ Pobieranie powiadomień dla użytkownika
    public function getNotificationsForUser($userId) {
        $stmt = $this->database->prepare('
            SELECT * FROM notifications
            WHERE user_id = :user_id
            ORDER BY created_at DESC
        ');
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];

        foreach ($notifications as $notification) {
            $result[] = new Notification(
                $notification['id'],
                $notification['user_id'],
                $notification['content'],
                $notification['is_read'],
                $notification['created_at']
            );
        }

        return $result;
    }

    // ✅ Oznaczanie powiadomień jako przeczytane
    public function markAsRead($notificationId) {
        $stmt = $this->database->prepare('
            UPDATE notifications
            SET is_read = true
            WHERE id = :id
        ');
        $stmt->bindParam(':id', $notificationId, PDO::PARAM_INT);
        $stmt->execute();
    }
}
