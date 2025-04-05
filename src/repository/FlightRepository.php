<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../models/Flight.php';

class FlightRepository {
    private $database;

    public function __construct() {
        $this->database = Database::getInstance()->connect();
    }

    public function getDatabase() {
        return $this->database;
    }

    public function addFlight($userId, $departureAirport, $landingAirport, $aircraft, $flightTime, $departureTime, $landingTime) {
        $stmt = $this->database->prepare('
            INSERT INTO flights (user_id, departure_airport, landing_airport, aircraft, flight_time, departure_time, landing_time)
            VALUES (:user_id, :departure_airport, :landing_airport, :aircraft, :flight_time, :departure_time, :landing_time)
        ');
    
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':departure_airport', $departureAirport, PDO::PARAM_STR);
        $stmt->bindParam(':landing_airport', $landingAirport, PDO::PARAM_STR);
        $stmt->bindParam(':aircraft', $aircraft, PDO::PARAM_STR);
        $stmt->bindParam(':flight_time', $flightTime, PDO::PARAM_INT);
        $stmt->bindParam(':departure_time', $departureTime, PDO::PARAM_STR);
        $stmt->bindParam(':landing_time', $landingTime, PDO::PARAM_STR);
    
        $stmt->execute();
    }
    
    public function deleteFlight($flightId, $userId) {
        $stmt = $this->database->prepare('
            DELETE FROM flights
            WHERE id = :flightId AND user_id = :userId
        ');
    
        $stmt->bindParam(':flightId', $flightId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    
        return $stmt->execute();
    }
    
    
    public function getFlightsByUserId($userId) {
        $stmt = $this->database->prepare('
            SELECT f.*, u.nickname 
            FROM flights f
            JOIN users u ON f.user_id = u.id
            WHERE f.user_id = :user_id
            ORDER BY f.created_at DESC
        ');
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    
        $flights = $stmt->fetchAll(PDO::FETCH_ASSOC); 
    
        $result = [];
    
        foreach ($flights as $flight) {
            $result[] = new Flight(
                $flight['id'],
                $flight['user_id'],
                $flight['departure_airport'],
                $flight['landing_airport'],
                $flight['aircraft'],
                $flight['flight_time'],
                $flight['departure_time'],
                $flight['landing_time'],
                $flight['nickname'],
                $flight['created_at']
            );
        }
    
        return $result;
    }
    
    public function getTotalFlightTimeByUserId($userId) {
        $stmt = $this->database->prepare('
            SELECT COALESCE(SUM(flight_time), 0) as total_time FROM flights WHERE user_id = :user_id
        ');
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // hh mm 
        $totalMinutes = (int) $result['total_time'];
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
    
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public function updateUserAvatar($userId, $avatarPath) {
        $stmt = $this->database->prepare('
            UPDATE users 
            SET avatar = :avatar 
            WHERE id = :userId
        ');
        $stmt->bindParam(':avatar', $avatarPath, PDO::PARAM_STR);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getMostUsedAirport($userId) {
        $stmt = $this->database->prepare('
            SELECT airport, COUNT(*) as count FROM (
                SELECT departure_airport AS airport FROM flights WHERE user_id = :userId
                UNION ALL
                SELECT landing_airport AS airport FROM flights WHERE user_id = :userId
            ) AS combined
            GROUP BY airport
            ORDER BY count DESC
            LIMIT 1
        ');
    
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result ? $result['airport'] : null;
    }
    
    
    public function getMostUsedAircraft($userId) {
        $stmt = $this->database->prepare('
            SELECT aircraft, COUNT(*) as count
            FROM flights
            WHERE user_id = :userId
            GROUP BY aircraft
            ORDER BY count DESC
            LIMIT 1
        ');
    
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result ? $result['aircraft'] : null;
    }    

    
}
