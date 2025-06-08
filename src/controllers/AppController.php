<?php
require_once __DIR__ . '/../repository/Database.php';
require_once __DIR__ . '/../repository/NotificationRepository.php';
require_once __DIR__ . '/../repository/MessageRepository.php';

class AppController {
    protected int $unreadNotifications = 0;
    protected int $unreadMessages = 0;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $notifRepo = new NotificationRepository();
        $this->unreadNotifications = $notifRepo->countUnreadNotifications($_SESSION['user_id'] ?? 0);
        if (isset($_SESSION['user_id'])) {
            $msgRepo = new MessageRepository();
            $this->unreadMessages = $msgRepo->countUnread($_SESSION['user_id']);
        }
    }

    protected function render(string $template = null, array $variables = []) {
        $variables['unreadCount'] = $this->unreadNotifications;
        $variables['messageUnreadCount'] = $this->unreadMessages;
        $templatePath = 'public/views/' . $template . '.php';
        if (file_exists($templatePath)) {
            extract($variables);
            include $templatePath;
        }
    }
    // wspólna metoda
    protected function getLoggedInUserId(): ?int {
        $stmt = Database::getInstance()
                        ->connect()
                        ->prepare('SELECT id FROM auth WHERE email = :email');
        $stmt->bindParam(':email', $_SESSION['user'], PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['id'] ?? null;
    }
}
