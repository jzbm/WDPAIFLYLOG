<?php
require_once 'AppController.php';
require_once __DIR__ . '/../repository/FlightRepository.php';

class FlightController extends AppController {
    private $flightRepository;

    public function __construct() {
        parent::__construct();
        $this->flightRepository = new FlightRepository();
    }

    // ✅ Dodawanie lotów
    public function add_flight() {
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit();
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $departureAirport = $_POST['departure_airport'];
            $landingAirport = $_POST['landing_airport'];
            $aircraft = $_POST['aircraft'];
            $departureTime = $_POST['departure_time'];
            $landingTime = $_POST['landing_time'];
    
            if (empty($departureAirport) || empty($landingAirport) || empty($aircraft) || empty($departureTime) || empty($landingTime)) {
                $error = "All fields are required!";
            } else {
                // ✅ Konwersja formatu daty na kompatybilny z PostgreSQL
                $departureTime = date('Y-m-d H:i:s', strtotime($departureTime));
                $landingTime = date('Y-m-d H:i:s', strtotime($landingTime));
    
                $departureTimestamp = strtotime($departureTime);
                $landingTimestamp = strtotime($landingTime);
    
                if ($landingTimestamp <= $departureTimestamp) {
                    $error = "Landing time must be after departure time!";
                } else {
                    // ✅ Obliczanie czasu lotu w minutach (jako integer)
                    $flightTime = (int)(($landingTimestamp - $departureTimestamp) / 60);
    
                    $userId = $this->get_user_id();
                    $this->flightRepository->addFlight($userId, $departureAirport, $landingAirport, $aircraft, $flightTime, $departureTime, $landingTime);
    
                    header("Location: /profile");
                    exit();
                }
            }
        }
    }
    public function delete_flight() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit();
        }
    
        $data = json_decode(file_get_contents('php://input'), true);
        $flightId = $data['flightId'] ?? null;
        $userId = $this->get_user_id();
    
        if (!$flightId) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid flight ID']);
            exit();
        }
    
        $success = $this->flightRepository->deleteFlight($flightId, $userId);
    
        if ($success) {
            http_response_code(200);
            echo json_encode(['message' => 'Flight deleted']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete flight']);
        }
    }
    
    // ✅ Przesyłanie awatara
    public function upload_Avatar() {
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
            $userId = $this->get_user_id();

            $targetDir = "uploads/avatars/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true); // ✅ Tworzymy katalog, jeśli nie istnieje
            }

            $fileName = basename($_FILES['avatar']['name']);
            $targetFilePath = $targetDir . $userId . "_" . $fileName;
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            // ✅ Dozwolone formaty plików
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($fileType, $allowedTypes)) {
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFilePath)) {
                    // ✅ Zaktualizuj ścieżkę awatara w bazie danych
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

    // ✅ Pobieranie danych użytkownika
    private function get_user_data() {
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
    
    
    // ✅ Łączny czas lotów
    public function get_total_flight_time() {
        $userId = $this->get_user_id();
        return $this->flightRepository->getTotalFlightTimeByUserId($userId);
    }

    // ✅ Pobieranie ID użytkownika
    private function get_user_id() {
        $stmt = $this->flightRepository->getDatabase()->prepare('
            SELECT id FROM auth WHERE email = :email
        ');
        $stmt->bindParam(':email', $_SESSION['user'], PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            die("User not found!");
        }

        return $user['id'] ?? null;
    }

    public function profile() {
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit();
        }
    
        $userId = $this->get_user_id();
        $user = $this->get_user_data();
        $flights = $this->flightRepository->getFlightsByUserId($userId);
        $totalFlightTime = $this->flightRepository->getTotalFlightTimeByUserId($userId);
    
        // ✅ Pobranie najczęściej używanego samolotu i lotniska
        $favouriteAircraft = $this->flightRepository->getMostUsedAircraft($userId);
        $favouriteAirport = $this->flightRepository->getMostUsedAirport($userId);
    
        $this->render('profile', [
            'user' => $user,
            'flights' => $flights,
            'totalFlightTime' => $totalFlightTime,
            'favouriteAircraft' => $favouriteAircraft ?? 'No data',
            'favouriteAirport' => $favouriteAirport ?? 'No data'
        ]);
    }
    
    
}
