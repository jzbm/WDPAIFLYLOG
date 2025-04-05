<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../models/Post.php';

class PostRepository {

    private $database;

    public function __construct() {
        $this->database = Database::getInstance()->connect(); 
    }
    
    public function getDatabase() {
        return $this->database;
    }
    

    public function add_Post($userId, $title, $content, $imagePath = null) {
        $stmt = $this->database->prepare('
            INSERT INTO posts (user_id, title, content, image) 
            VALUES (:userId, :title, :content, :image)
        ');
    
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);
        $stmt->bindParam(':image', $imagePath, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function get_Posts($userId = null) {
        $stmt = $this->database->prepare('
            SELECT p.id, p.user_id, p.title, p.content, p.image, p.created_at, u.nickname, u.avatar,
                   (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS likes_count,
                   COALESCE((
                       SELECT 1 FROM likes WHERE post_id = p.id AND user_id = :userId
                   ), 0) AS is_liked
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            ORDER BY p.id DESC
        ');
    
        $stmt->bindValue(':userId', $userId ?? 0, PDO::PARAM_INT);
        $stmt->execute();
    
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $result = [];
        foreach ($posts as $post) {
            $postObject = new Post(
                $post['id'],
                $post['user_id'],
                $post['title'],
                $post['created_at'],
                $post['content'],
                $post['nickname'],
                $post['image'] ?? null,
                $post['avatar'] ?? null
            );
    
            $postObject->setLikesCount((int) $post['likes_count']);
            $postObject->setIsLikedByUser((bool) $post['is_liked']);
    
            $result[] = $postObject;
        }
    
        return $result;
    }
    
    
    
    public function likePost($postId, $userId) {
        $stmt = $this->database->prepare('
            INSERT INTO likes (post_id, user_id) 
            VALUES (:postId, :userId)
            ON CONFLICT (post_id, user_id) DO NOTHING
        ');
        $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function unlikePost($postId, $userId) {
        $stmt = $this->database->prepare('
            DELETE FROM likes 
            WHERE post_id = :postId AND user_id = :userId
        ');
        $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getLikesCount($postId) {
        $stmt = $this->database->prepare('
            SELECT COUNT(*) AS count FROM likes WHERE post_id = :postId
        ');
        $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    public function isLikedByUser($postId, $userId) {
        $stmt = $this->database->prepare('
            SELECT COUNT(*) AS count FROM likes WHERE post_id = :postId AND user_id = :userId
        ');
        $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }

    public function deletePostById(int $postId): void {
        $stmt = $this->database->prepare('
            DELETE FROM posts WHERE id = :id
        ');
        $stmt->bindParam(':id', $postId, PDO::PARAM_INT);
        $stmt->execute();
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

}
