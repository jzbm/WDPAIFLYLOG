<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../repository/NotificationRepository.php';
require_once __DIR__ . '/../repository/PostRepository.php';

class CommentRepository {
    private $database;
    private $notificationRepository;
    private $postRepository;

    public function __construct() {
        $this->database = Database::getInstance()->connect();
        $this->notificationRepository = new NotificationRepository();
        $this->postRepository = new PostRepository();
    }

    public function addComment($postId, $userId, $content) {
        try {
            $this->database->beginTransaction();

            $stmt = $this->database->prepare('
                INSERT INTO comments (post_id, user_id, content) 
                VALUES (:postId, :userId, :content)
            ');
            $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':content', $content, PDO::PARAM_STR);
            $stmt->execute();
            $this->database->commit();
        } catch (Exception $e) {
            $this->database->rollBack();
            throw $e;
        }
    }

    public function getCommentsByPostId($postId) {
        $stmt = $this->database->prepare('
            SELECT c.*, u.nickname, u.avatar
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
                $comment['nickname'],
                $comment['avatar'] ?? null
            );
        }

        return $result;
    }

    public function getLatestCommentByPostAndUser($postId, $userId) {
        $stmt = $this->database->prepare('
            SELECT c.*, u.nickname, u.avatar 
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.post_id = :post_id AND c.user_id = :user_id
            ORDER BY c.created_at DESC
            LIMIT 1
        ');
        $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($comment) {
            return new Comment(
                $comment['id'],
                $comment['post_id'],
                $comment['user_id'],
                $comment['content'],
                $comment['created_at'],
                $comment['nickname'],
                $comment['avatar'] ?? null
            );
        }
        
        return null;
    }

    public function getPostAuthorId($postId) {
        $stmt = $this->database->prepare('
            SELECT user_id FROM posts WHERE id = :post_id
        ');
        $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['user_id'] ?? null;
    }

    public function deleteComment($commentId) {
        try {
            $stmt = $this->database->prepare('DELETE FROM comments WHERE id = :comment_id');
            $stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error deleting comment: " . $e->getMessage());
            return false;
        }
    }
}
