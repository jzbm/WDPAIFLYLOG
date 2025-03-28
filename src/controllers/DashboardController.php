<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/PostRepository.php';
require_once __DIR__ . '/../repository/CommentRepository.php';

class DashboardController extends AppController {
    private $postRepository;
    private $commentRepository;

    public function __construct() {
        parent::__construct();
        $this->postRepository = new PostRepository();
        $this->commentRepository = new CommentRepository();
    }

    // ✅ Dashboard dostępny dla każdego użytkownika
    public function dashboard() {
        $userId = isset($_SESSION['user']) ? $this->get_user_id() : null;
        $posts = $this->postRepository->get_Posts($userId);

        foreach ($posts as $post) {
            $comments = $this->commentRepository->getCommentsByPostId($post->getId());
            $post->setComments($comments);
        }

        $this->render('dashboard', ['posts' => $posts]);
    }

    // ✅ Dodawanie posta dostępne tylko dla zalogowanych użytkowników
    public function add_post() {
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $this->get_user_id();
            $title = $_POST['title'];
            $content = $_POST['content'];
            $imagePath = null;

            if (!empty($_FILES['image']['name'])) {
                $targetDir = "uploads/";
                $imagePath = $targetDir . basename($_FILES['image']['name']);
                move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
            }

            $this->postRepository->add_Post($userId, $title, $content, $imagePath);
            header("Location: /dashboard");
            exit();
        }
    }

    // ✅ Funkcja do pobierania ID użytkownika
    private function get_user_id() {
        $stmt = $this->postRepository->getDatabase()->prepare('
            SELECT id FROM auth WHERE email = :email
        ');
        $stmt->bindParam(':email', $_SESSION['user'], PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user['id'] ?? null;
    }

    // ✅ Funkcja do polubienia posta
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
}
