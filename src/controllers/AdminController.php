<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/UserRepository.php';
require_once __DIR__ . '/../repository/Database.php';

class AdminController extends AppController {
    private $userRepository;
    private $db;

    public function __construct() {
        parent::__construct();
        $this->userRepository = new UserRepository();
        $this->db = Database::getInstance()->connect();
    }

    public function user_management() {
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            die("Access denied.");
        }

        $nickname = $_GET['nickname'] ?? null;

        if ($nickname) {
            $users = $this->userRepository->getUsersByNickname($nickname);
        } else {
            $users = $this->userRepository->getAllUsersWithRoles();
        }        
        $this->render('user-management', ['users' => $users]);
    }

    public function delete_user() {
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            die("Access denied.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
            $userId = (int)$_POST['user_id'];

            // zabezpiecznie
            if ($userId === (int)$this->get_user_id()) {
                http_response_code(400);
                die("You cannot delete yourself.");
            }

            $this->userRepository->deleteUserById($userId);
            header("Location: /user-management");
            exit();
        }
    }

    private function get_user_id() {
        $stmt = $this->db->prepare('SELECT id FROM auth WHERE email = :email');
        $stmt->bindParam(':email', $_SESSION['user'], PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user['id'] ?? null;
    }
}
