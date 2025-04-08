<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/FlightRepository.php';
require_once __DIR__ . '/../models/User.php';

class ProfileController extends AppController {
    private FlightRepository $flightRepository;

    public function __construct() {
        parent::__construct();
        $this->flightRepository = new FlightRepository();
    }

    public function profile() {
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit();
        }
    
        $userId = $this->get_user_id();
        $userData = $this->get_user_data();
        $flights = $this->flightRepository->getFlightsByUserId($userId);
        $totalFlightTime = $this->flightRepository->getTotalFlightTimeByUserId($userId);
    
        $favouriteAircraft = $this->flightRepository->getMostUsedAircraft($userId);
        $favouriteAirport = $this->flightRepository->getMostUsedAirport($userId);
    

        $user = new User(
            $userId,
            $userData['email'],
            '', 
            $userData['nickname'],
            0,
            $userData['avatar']
        );
    
        $this->render('profile', [
            'user' => $user,
            'flights' => $flights,
            'totalFlightTime' => $totalFlightTime,
            'favouriteAircraft' => $favouriteAircraft ?? 'No data',
            'favouriteAirport' => $favouriteAirport ?? 'No data'
        ]);
    }
    

    public function upload_Avatar() {
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
            $userId = $this->get_user_id();

            $targetDir = "uploads/avatars/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $fileName = basename($_FILES['avatar']['name']);
            $targetFilePath = $targetDir . $userId . "_" . $fileName;
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($fileType, $allowedTypes)) {
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFilePath)) {
                    $this->flightRepository->updateUserAvatar($userId, $targetFilePath);
                    header("Location: /profile");
                    exit();
                } else {
                    echo "Błąd podczas przesyłania pliku!";
                }
            } else {
                echo "Niedozwolony format pliku!";
            }
        }
    }

    private function get_user_data(): array {
        $stmt = $this->flightRepository->getDatabase()->prepare('
            SELECT u.nickname, u.avatar, a.email
            FROM users u
            JOIN auth a ON u.id = a.id
            WHERE a.email = :email
        ');
        $stmt->bindParam(':email', $_SESSION['user'], PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function get_user_id(): int {
        $stmt = $this->flightRepository->getDatabase()->prepare('
            SELECT id FROM auth WHERE email = :email
        ');
        $stmt->bindParam(':email', $_SESSION['user'], PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            die("User not found!");
        }

        return $user['id'];
    }
}