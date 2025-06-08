<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/PostRepository.php';
require_once __DIR__ . '/../repository/CommentRepository.php';
require_once __DIR__ . '/../repository/FlightRepository.php';

class PostController extends AppController {
    private $postRepository;
    private $commentRepository;

    public function __construct() {
        parent::__construct();
        $this->postRepository    = new PostRepository();
        $this->commentRepository = new CommentRepository();
    }

    public function dashboard() {
        $userId = $this->getLoggedInUserId();
        $posts  = $this->postRepository->get_Posts($userId);
        $visibleCommentsMap = [];
        $hiddenCommentsMap = [];
        $hiddenCountMap = [];
        foreach ($posts as $post) {
            $allComments = $this->commentRepository->getCommentsByPostId($post->getId());
            $post->setComments($allComments);
            $visibleCommentsMap[$post->getId()] = array_slice($allComments, 0, 2);
            $hiddenCommentsMap[$post->getId()] = array_slice($allComments, 2);
            $hiddenCountMap[$post->getId()] = count($hiddenCommentsMap[$post->getId()]);
        }

        // pobranie statystyk globalnych
        $statsRepo = new FlightRepository();
        $stats     = $statsRepo->getGlobalFlightStats();

        $this->render('dashboard', [
            'posts'               => $posts,
            'stats'               => $stats,
            'visibleCommentsMap'  => $visibleCommentsMap,
            'hiddenCommentsMap'   => $hiddenCommentsMap,
            'hiddenCountMap'      => $hiddenCountMap
        ]);
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
                $userId = $this->getLoggedInUserId();   // zmiana
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
            $userId = $this->getLoggedInUserId();      // zmiana

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
}
