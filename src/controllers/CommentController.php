<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/CommentRepository.php';
require_once __DIR__ . '/../repository/NotificationRepository.php';
require_once __DIR__ . '/../repository/UserRepository.php';


class CommentController extends AppController {
    private $commentRepository;
    private $userRepository;
    private $notificationRepository;

    public function __construct() {
        parent::__construct();
        $this->commentRepository   = new CommentRepository();
        $this->userRepository      = new UserRepository();
        $this->notificationRepository = new NotificationRepository();
    }

    public function add_comment() {
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit();
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postId = $_POST['post_id'];
            $content = $_POST['content'];
    
            if (!empty($postId) && !empty($content)) {
                $userId = $this->getLoggedInUserId();

                $this->commentRepository->addComment($postId, $userId, $content);

                $authorId = $this->commentRepository->getPostAuthorId($postId);
                if ($authorId && $authorId !== $userId) {
                    $nickname = $this->userRepository->getUserById($userId)['nickname'] ?? 'Unknown';
                    $message = "User <strong>$nickname</strong> commented on <a href='/dashboard#post-$postId'>your post</a>.";

                    $this->notificationRepository->createNotification($authorId, $message);
                }

                header("Location: /dashboard");
                exit();
            }
        }
    }
}
