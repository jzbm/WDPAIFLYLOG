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

    public function add_comment_ajax() {
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postId = $_POST['post_id'] ?? null;
            $content = $_POST['content'] ?? null;

            if (!empty($postId) && !empty($content)) {
                $userId = $this->getLoggedInUserId();
                $this->commentRepository->addComment($postId, $userId, $content);
                $newComment = $this->commentRepository->getLatestCommentByPostAndUser($postId, $userId);
                $authorId = $this->commentRepository->getPostAuthorId($postId);
                if ($authorId && $authorId !== $userId) {
                    $nickname = $this->userRepository->getUserById($userId)['nickname'] ?? 'Unknown';
                    $message = "User <strong>$nickname</strong> commented on <a href='/dashboard#post-$postId'>your post</a>.";
                    $this->notificationRepository->createNotification($authorId, $message);
                }
                echo json_encode([
                    'success' => true,
                    'comment' => [
                        'id' => $newComment->getId(),
                        'content' => htmlspecialchars($newComment->getContent()),
                        'nickname' => htmlspecialchars($newComment->getNickname()),
                        'avatar' => htmlspecialchars($newComment->getAvatar()),
                        'userId' => $newComment->getUserId(),
                        'createdAt' => $newComment->getCreatedAt() ? date('d.m.Y, H:i', strtotime($newComment->getCreatedAt())) : ''
                    ]
                ]);
                exit();
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                exit();
            }
        }
    }
}
