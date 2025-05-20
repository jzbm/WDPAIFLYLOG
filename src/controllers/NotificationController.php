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

        $userId = $this->getLoggedInUserId();            // nowa metoda
        $notifications = $this->notificationRepository
                              ->getNotificationsForUser($userId);

        $this->render('notifications', [
            'notifications' => $notifications
        ]);
    }

    public function mark_all_as_read() {
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit();
        }
    
        $userId = $this->getLoggedInUserId();
        $this->notificationRepository->markAllAsRead($userId);
        header("Location: /notifications");
        exit();
    }
}
