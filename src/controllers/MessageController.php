<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/MessageRepository.php';

class MessageController extends AppController {
    private $messageRepository;

    public function __construct() {
        parent::__construct();
        $this->messageRepository = new MessageRepository();
    }

    public function messages() {
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit();
        }

        $userId = $_SESSION['user_id'];
        $messages = $this->messageRepository->getMessagesByUserId($userId);
        $users = $this->messageRepository->getAllUsers();

        $this->render('messages', [
            'messages' => $messages,
            'users' => $users
        ]);
    }

    public function send_message() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $senderId = $_SESSION['user_id'];
            $receiverId = $_POST['receiver_id'];
            $content = $_POST['content'];

            if (!empty($receiverId) && !empty($content)) {
                $this->messageRepository->sendMessage($senderId, $receiverId, $content);
                header("Location: /messages");
                exit();
            }
        }
    }
}
