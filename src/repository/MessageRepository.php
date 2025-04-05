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
            "SELECT 
                u.id,
                u.nickname,
                u.avatar,
                m.content AS lastMessage,
                m.created_at AS timestamp
            FROM (
                SELECT 
                    LEAST(sender_id, receiver_id) AS user1,
                    GREATEST(sender_id, receiver_id) AS user2,
                    MAX(created_at) AS latest
                FROM messages
                GROUP BY LEAST(sender_id, receiver_id), GREATEST(sender_id, receiver_id)
            ) AS conversation_groups
            INNER JOIN messages m 
                ON ((LEAST(m.sender_id, m.receiver_id) = conversation_groups.user1)
                 AND (GREATEST(m.sender_id, m.receiver_id) = conversation_groups.user2)
                 AND m.created_at = conversation_groups.latest)
            INNER JOIN users u
                ON (
                    (conversation_groups.user1 = :userId AND u.id = conversation_groups.user2)
                    OR
                    (conversation_groups.user2 = :userId AND u.id = conversation_groups.user1)
                )
            WHERE :userId IN (conversation_groups.user1, conversation_groups.user2)
            ORDER BY m.created_at DESC"
        );
    
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    

    public function getUserByNickname($nickname) {
        $stmt = $this->database->prepare('SELECT id FROM users WHERE nickname = :nickname LIMIT 1');
        $stmt->bindParam(':nickname', $nickname, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
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

}
