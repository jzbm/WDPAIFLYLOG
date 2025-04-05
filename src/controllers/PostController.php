<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/PostRepository.php';
require_once __DIR__ . '/../repository/CommentRepository.php';

class PostController extends AppController {
    private $postRepository;
    private $commentRepository;

    public function __construct() {
        parent::__construct();
        $this->postRepository = new PostRepository();
        $this->commentRepository = new CommentRepository();
    }

    public function dashboard() {
        $userId = isset($_SESSION['user']) ? $this->get_user_id() : null;
        $posts = $this->postRepository->get_Posts($userId);

        foreach ($posts as $post) {
            $comments = $this->commentRepository->getCommentsByPostId($post->getId());
            $post->setComments($comments);
        }

        $this->render('dashboard', ['posts' => $posts]);
    }

    public function add_post() {
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $content = $_POST['content'];

            $imagePath = null;

            if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $targetDir = "uploads/";
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                $imageName = basename($_FILES['image']['name']);
                $targetFilePath = $targetDir . time() . '_' . $imageName;
                $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($fileType, $allowedTypes)) {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                        $imagePath = $targetFilePath;
                    }
                }
            }

            if (empty($title) || empty($content)) {
                $error = "Title and content cannot be empty!";
            } else {
                $userId = $this->get_user_id();
                $this->postRepository->add_Post($userId, $title, $content, $imagePath);
                header("Location: /dashboard");
                exit();
            }
        }
    }

    // Like / Unlike
    public function like_post() {
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postId = $_POST['post_id'];
            $userId = $this->get_user_id();

            if (!$postId || !$userId) {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid data']);
                exit();
            }

            if ($this->postRepository->isLikedByUser($postId, $userId)) {
                $this->postRepository->unlikePost($postId, $userId);
                $liked = false;
            } else {
                $this->postRepository->likePost($postId, $userId);
                $liked = true;
            }

            $likesCount = $this->postRepository->getLikesCount($postId);
            echo json_encode(['liked' => $liked, 'likesCount' => $likesCount]);
            exit();
        }
    }
    
    private function get_nickname() {
        $stmt = $this->postRepository->getDatabase()->prepare('
            SELECT nickname FROM users WHERE id = (
                SELECT id FROM auth WHERE email = :email
            )
        ');
        $stmt->bindParam(':email', $_SESSION['user'], PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['nickname'] ?? '';
    }
    

    public function delete_post() {
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            die("Access denied.");
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
            $postId = (int)$_POST['post_id'];
            $this->postRepository->deletePostById($postId);
    
            header("Location: /dashboard");
            exit();
        }
    }
    
    // id session
    private function get_user_id() {
        $stmt = $this->postRepository->getDatabase()->prepare('SELECT id FROM auth WHERE email = :email');
        $stmt->bindParam(':email', $_SESSION['user'], PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user['id'] ?? null;
    }
    
}
