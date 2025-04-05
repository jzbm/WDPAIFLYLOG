<?php
require_once 'AppController.php';
require_once __DIR__ . '/../repository/FlightRepository.php';
require_once __DIR__ . '/../models/User.php';


class FlightController extends AppController {
    private $flightRepository;

    public function __construct() {
        parent::__construct();
        $this->flightRepository = new FlightRepository();
    }

  
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
                
                $departureTime = date('Y-m-d H:i:s', strtotime($departureTime));
                $landingTime = date('Y-m-d H:i:s', strtotime($landingTime));
    
                $departureTimestamp = strtotime($departureTime);
                $landingTimestamp = strtotime($landingTime);
    
                if ($landingTimestamp <= $departureTimestamp) {
                    $error = "Landing time must be after departure time!";
                } else {
                    
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
    
    public function get_total_flight_time() {
        $userId = $this->get_user_id();
        return $this->flightRepository->getTotalFlightTimeByUserId($userId);
    }

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
    
}