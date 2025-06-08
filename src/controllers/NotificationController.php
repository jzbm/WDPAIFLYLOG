<?php

require_once __DIR__ . '/AppController.php';
require_once __DIR__ . '/../repository/NotificationRepository.php';

class NotificationController extends AppController {
    private NotificationRepository $notificationRepository;

    public function __construct() {
        parent::__construct();
        $this->notificationRepository = new NotificationRepository();
    }
    
    public function notifications() {
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit();
        }

        $userId         = $this->getLoggedInUserId();
        $notifications  = $this->notificationRepository->getNotificationsForUser($userId);
        $unreadCount    = $this->getUnreadCount();

        $this->render('notifications', [
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount
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

    public function getUnreadCount(): int {
        if (!isset($_SESSION['user'])) {
            return 0;
        }
        $userId = $this->getLoggedInUserId();
        return $this->notificationRepository->countUnreadNotifications($userId);
    }
}
