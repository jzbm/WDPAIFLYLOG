<?php
require_once __DIR__ . '/../repository/Database.php';

class AppController {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function render(string $template = null, array $variables = []) {
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
