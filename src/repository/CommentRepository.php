<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../models/Comment.php';

class CommentRepository {
    private $database;

    public function __construct() {
        $this->database = Database::getInstance()->connect();
    }

    public function addComment($postId, $userId, $content) {
        $stmt = $this->database->prepare('
            INSERT INTO comments (post_id, user_id, content) 
            VALUES (:postId, :userId, :content)
        ');

        $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function getCommentsByPostId($postId) {
        $stmt = $this->database->prepare('
            SELECT c.*, u.nickname 
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.post_id = :postId
            ORDER BY c.created_at ASC
        ');

        $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();

        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($comments as $comment) {
            $result[] = new Comment(
                $comment['id'],
                $comment['post_id'],
                $comment['user_id'],
                $comment['content'],
                $comment['created_at'],
                $comment['nickname']
            );
        }

        return $result;
    }
}
