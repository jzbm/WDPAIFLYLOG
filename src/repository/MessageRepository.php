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
            SELECT id, email FROM auth
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
}
