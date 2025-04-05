<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/NotificationRepository.php';

class NotificationController extends AppController {
    private $notificationRepository;

    public function __construct() {
        parent::__construct();
        $this->notificationRepository = new NotificationRepository();
    }
    
    public function notifications() {
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit();
        }

        $userId = $this->get_user_id();
        $notifications = $this->notificationRepository->getNotificationsForUser($userId);

        $this->render('notifications', [
            'notifications' => $notifications
        ]);
    }

    public function mark_all_as_read() {
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit();
        }
    
        $userId = $this->get_user_id();
        $this->notificationRepository->markAllAsRead($userId);
        header("Location: /notifications");
        exit();
    }
    

    private function get_user_id() {
        $stmt = $this->notificationRepository->getDatabase()->prepare('
            SELECT id FROM auth WHERE email = :email
        ');
        $stmt->bindParam(':email', $_SESSION['user'], PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user['id'] ?? null;
    }
}
