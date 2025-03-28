<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/UserRepository.php';

class SecurityController extends AppController {
    private $userRepository;

    public function __construct() {
        parent::__construct();
        $this->userRepository = new UserRepository();
    }

    public function login() {
        $error = null;

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $auth = $this->userRepository->getAuthByEmail($email);
            if ($auth && password_verify($password, $auth['password'])) {
                $user = $this->userRepository->getUserById($auth['id']);

                $_SESSION['user'] = $auth['email'];
                $_SESSION['user_id'] = $auth['id'];
                $_SESSION['nickname'] = $user['nickname'];
                $_SESSION['role'] = $this->userRepository->getUserRoleById($auth['id']);

                header("Location: /dashboard");
                exit();
            } else {
                $error = "Invalid email or password!";
            }
        }

        $this->render('login', ['error' => $error]);
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: /login");
        exit();
    }

    public function register() {
        $error = null;

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];
            $nickname = $_POST['nickname'];

            if ($password !== $confirmPassword) {
                $error = "Passwords do not match!";
            } else {
                $existingAuth = $this->userRepository->getAuthByEmail($email);

                if ($existingAuth) {
                    $error = "Email already in use!";
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $userId = $this->userRepository->createAuth($email, $hashedPassword);
                    $this->userRepository->createUser($userId, $nickname);

                    header("Location: /login");
                    exit();
                }
            }
        }

        $this->render('register', ['error' => $error]);
    }
}
