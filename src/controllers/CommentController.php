<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/CommentRepository.php';
require_once __DIR__ . '/../repository/NotificationRepository.php';


class CommentController extends AppController {
    private $commentRepository;

    public function __construct() {
        parent::__construct();
        $this->commentRepository = new CommentRepository();
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
                $userId = $this->get_user_id();
    
                $this->commentRepository->addComment($postId, $userId, $content);

                $authorId = $this->commentRepository->getPostAuthorId($postId);
                if ($authorId && $authorId !== $userId) {
                    $notificationRepo = new NotificationRepository();
    
                    $nickname = $this->commentRepository->getUserNicknameById($userId);
                    $message = "Użytkownik <strong>$nickname</strong> skomentował <a href='/dashboard#post-$postId'>twój post</a>.";
    
                    $notificationRepo->createNotification($authorId, $message);
                }
    
                header("Location: /dashboard");
                exit();
            }
        }
    }
    

    private function get_user_id() {
        $db = Database::getInstance()->connect();

        $stmt = $db->prepare('
            SELECT id FROM auth WHERE email = :email
        ');
        $stmt->bindParam(':email', $_SESSION['user'], PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user['id'] ?? null;
    }
}
