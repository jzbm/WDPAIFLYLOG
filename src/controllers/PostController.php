<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/PostRepository.php';

class PostController extends AppController {
    private $postRepository;

    public function __construct() {
        parent::__construct();
        $this->postRepository = new PostRepository();
    }

    private function get_user_id() { // ✅ snake_case
        $db = Database::getInstance()->connect();
    
        $stmt = $db->prepare('
            SELECT id FROM auth WHERE email = :email
        ');
        $stmt->bindParam(':email', $_SESSION['user'], PDO::PARAM_STR);
        $stmt->execute();
    
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user['id'] ?? null;
    }
    
    public function add_post() { 
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $content = $_POST['content'];

            $imagePath = null;

            // ✅ Obsługa przesyłania zdjęcia
            if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $targetDir = "uploads/";
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
            
                $imageName = basename($_FILES['image']['name']);
                $targetFilePath = $targetDir . time() . '_' . $imageName;
                $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
            
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($fileType, $allowedTypes)) {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                        $imagePath = $targetFilePath;
                    }
                }
            }
            

            if (empty($title) || empty($content)) {
                $error = "Title and content cannot be empty!";
            } else {
                $userId = $this->get_user_id();
                $this->postRepository->add_Post($userId, $title, $content, $imagePath);

                header("Location: /dashboard");
                exit();
            }
        }
    }
}
