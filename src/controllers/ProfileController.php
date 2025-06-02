<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/FlightRepository.php';
require_once __DIR__ . '/../repository/UserRepository.php';
require_once __DIR__ . '/../models/User.php';

class ProfileController extends AppController {
    private FlightRepository $flightRepository;
    private UserRepository   $userRepository;

    public function __construct() {
        parent::__construct();
        $this->flightRepository = new FlightRepository();
        $this->userRepository   = new UserRepository();
    }

    public function profile() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $userId   = $_SESSION['user_id'];
        $userData = $this->userRepository->getUserById($userId);
        $flights            = $this->flightRepository->getFlightsByUserId($userId);
        $totalFlightTime    = $this->flightRepository->getTotalFlightTimeByUserId($userId);
        $favouriteAircraft  = $this->flightRepository->getMostUsedAircraft($userId) ?? 'No data';
        $favouriteAirport   = $this->flightRepository->getMostUsedAirport($userId)  ?? 'No data';

        $user = new User(
            $userId,
            $userData['email'],
            '',
            $userData['nickname'],        
            strtolower($userData['role']),
            $userData['avatar'] ?? null
        );

        // ProfileController::profile()
        require_once __DIR__ . '/NotificationController.php';
        $unreadCount = (new NotificationController())->getUnreadCount();

        $this->render('profile', [
            'user'               => $user,
            'flights'            => $flights,
            'totalFlightTime'    => $totalFlightTime,
            'favouriteAircraft'  => $favouriteAircraft,
            'favouriteAirport'   => $favouriteAirport,
            'unreadCount'        => $unreadCount
        ]);
    }

    public function upload_avatar() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
            $auth   = $this->userRepository->getAuthByEmail($_SESSION['user']);
            $userId = (int)$auth['id'];

            $dir = 'uploads/avatars/';
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            $filename = basename($_FILES['avatar']['name']);
            $ext      = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $allowed  = ['jpg','jpeg','png','gif'];

            if (!in_array($ext, $allowed)) {
                echo 'File format not allowed!';
                return;
            }

            $target = $dir . $userId . '_' . $filename;
            if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
                echo 'Error uploading file!';
                return;
            }
            $this->userRepository->updateUserAvatar($userId, $target);
            header('Location: /profile');
            exit;
        }
    }
}
