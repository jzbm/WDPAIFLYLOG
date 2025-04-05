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
    public function createNotification($userId, $content) {
        $stmt = $this->database->prepare('
            INSERT INTO notifications (user_id, content) VALUES (:user_id, :content)
        ');
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);
        $stmt->execute();
    }    

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
    
    public function markAllAsRead($userId) {
        $stmt = $this->database->prepare('
            UPDATE notifications
            SET is_read = true
            WHERE user_id = :user_id
        ');
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }


    public function countUnreadNotifications($userId) {
        $stmt = $this->database->prepare('
            SELECT COUNT(*) as unread_count FROM notifications
            WHERE user_id = :user_id AND is_read = false
        ');
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['unread_count'] ?? 0;
    }
    
}
