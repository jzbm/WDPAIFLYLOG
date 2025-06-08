<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/MessageRepository.php';
require_once __DIR__ . '/NotificationController.php';

class MessageController extends AppController {
    private $messageRepository;

    public function __construct() {
        parent::__construct();
        $this->messageRepository = new MessageRepository();
    }

    public function messages() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit();
        }

        $userId = $_SESSION['user_id'];
        $recentUsers = $this->messageRepository->getRecentConversationsForUser($userId);
        foreach ($recentUsers as &$u) {
            $u['unreadCount'] = $this->messageRepository->countUnreadFrom($userId, $u['id']);
        }
        unset($u);
        $users = $this->messageRepository->getAllUsers();

        $selectedUserId = isset($_GET['user']) ? intval($_GET['user']) : null;
        $selectedMessages = [];

        if ($selectedUserId) {
            $selectedMessages = $this->messageRepository->getMessagesBetweenUsers($userId, $selectedUserId);
            $this->messageRepository->markAsRead($userId, $selectedUserId);
        }

        $notifCtrl   = new NotificationController();
        $unreadCount = $notifCtrl->getUnreadCount();

        $messageUnreadCount = $this->messageRepository->countUnread($userId);
        $this->render('messages', [
            'recentUsers' => $recentUsers,
            'users' => $users,
            'selectedMessages' => $selectedMessages,
            'selectedUserId' => $selectedUserId,
            'unreadCount'=> $unreadCount,
            'messageUnreadCount' => $messageUnreadCount,
        ]);
    }

    public function send_message() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $senderId = $_SESSION['user_id'];
            $receiverId = $_POST['receiver_id'] ?? null;
            $content = trim($_POST['content'] ?? '');

            if ($receiverId && !empty($content)) {
                $this->messageRepository->sendMessage($senderId, $receiverId, $content);
                header("Location: /messages?user=" . $receiverId);
                exit();
            } else {
                $_SESSION['error_message'] = "User not found or message is empty.";
                header("Location: /messages");
                exit();
            }
        }
    }

    public function get_messages_ajax() {
        if (!isset($_SESSION['user_id']) || !isset($_GET['user'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing parameters']);
            exit();
        }
    
        $userId = $_SESSION['user_id'];
        $otherId = intval($_GET['user']);
        $messages = $this->messageRepository->getMessagesBetweenUsers($userId, $otherId);
    
        header('Content-Type: application/json');
        echo json_encode(array_map(fn($m) => [
            'sender_id' => $m->getSenderId(),
            'content' => $m->getContent(),
            'created_at' => $m->getCreatedAt()
        ], $messages));
        exit();
    }
    
}
