<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../models/Message.php';

class MessageRepository {
    private $database;

    public function __construct() {
        $this->database = Database::getInstance()->connect();
    }

    public function getDatabase() {
        return $this->database;
    }

    public function getAllUsers() {
        $stmt = $this->database->prepare('
            SELECT id, nickname FROM users
            WHERE id != :currentUserId
        ');

        $stmt->bindParam(':currentUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMessagesByUserId($userId) {
        $stmt = $this->database->prepare('
            SELECT * FROM messages 
            WHERE sender_id = :userId OR receiver_id = :userId 
            ORDER BY created_at DESC
        ');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];

        foreach ($messages as $message) {
            $result[] = new Message(
                $message['id'],
                $message['sender_id'],
                $message['receiver_id'],
                $message['content'],
                $message['created_at']
            );
        }

        return $result;
    }

    public function sendMessage($senderId, $receiverId, $content) {
        $stmt = $this->database->prepare('
            INSERT INTO messages (sender_id, receiver_id, content) 
            VALUES (:senderId, :receiverId, :content)
        ');

        $stmt->bindParam(':senderId', $senderId, PDO::PARAM_INT);
        $stmt->bindParam(':receiverId', $receiverId, PDO::PARAM_INT);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);

        $stmt->execute();
    }

    public function getRecentConversationsForUser($userId) {
        $stmt = $this->database->prepare(
            "SELECT id, nickname, avatar, lastMessage, timestamp
             FROM recent_conversations
             WHERE (user1 = :userId OR user2 = :userId)
               AND id != :userId
             ORDER BY timestamp DESC"
        );
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getMessagesBetweenUsers($userId1, $userId2) {
        $stmt = $this->database->prepare(
            'SELECT * FROM messages 
             WHERE (sender_id = :user1 AND receiver_id = :user2)
                OR (sender_id = :user2 AND receiver_id = :user1)
             ORDER BY created_at ASC'
        );

        $stmt->execute([
            'user1' => $userId1,
            'user2' => $userId2
        ]);

        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];

        foreach ($messages as $message) {
            $result[] = new Message(
                $message['id'],
                $message['sender_id'],
                $message['receiver_id'],
                $message['content'],
                $message['created_at']
            );
        }

        return $result;
    }

    public function countUnread(int $userId): int {
        $stmt = $this->database->prepare(
            'SELECT COUNT(*) FROM messages WHERE receiver_id = :uid AND is_read = false'
        );
        $stmt->execute([':uid' => $userId]);
        return (int)$stmt->fetchColumn();
    }
    
    public function markAsRead(int $userId, int $otherId): void {
        $stmt = $this->database->prepare(
            'UPDATE messages SET is_read = true WHERE receiver_id = :uid AND sender_id = :other'
        );
        $stmt->execute([':uid' => $userId, ':other' => $otherId]);
    }

    public function countUnreadFrom(int $userId, int $otherId): int {
        $stmt = $this->database->prepare(
            'SELECT COUNT(*) FROM messages
             WHERE receiver_id = :uid
               AND sender_id   = :other
               AND is_read     = false'
        );
        $stmt->execute([':uid' => $userId, ':other' => $otherId]);
        return (int)$stmt->fetchColumn();
    }

}
