<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../models/Flight.php';

class FlightRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->connect();
    }

    public function addFlight(
        int $userId,
        string $departureAirport,
        string $landingAirport,
        string $aircraft,
        int $flightTime,
        string $departureTime,
        string $landingTime
    ): void {
        $this->db->prepare(
            'INSERT INTO flights (
                user_id, departure_airport, landing_airport,
                aircraft, flight_time, departure_time, landing_time
            ) VALUES (
                :user_id, :dep, :land,
                :ac,       :time, :dep_t,       :land_t
            )'
        )->execute([
            ':user_id' => $userId,
            ':dep'     => $departureAirport,
            ':land'    => $landingAirport,
            ':ac'      => $aircraft,
            ':time'    => $flightTime,
            ':dep_t'   => $departureTime,
            ':land_t'  => $landingTime,
        ]);
    }

    public function deleteFlight(int $flightId, int $userId): bool
    {
        return $this->db
            ->prepare('DELETE FROM flights WHERE id = :fid AND user_id = :uid')
            ->execute([':fid' => $flightId, ':uid' => $userId]);
    }

    /**
     * Pobiera loty z widoku v_flights i ręcznie tworzy obiekty Flight
     *
     * @param int $userId
     * @return Flight[]
     */
    public function getFlightsByUserId(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM v_flights WHERE user_id = :uid ORDER BY created_at DESC'
        );
        $stmt->execute([':uid' => $userId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $result = [];
        foreach ($rows as $f) {
            $result[] = new Flight(
                $f['id'],
                $f['user_id'],
                $f['departure_airport'],
                $f['landing_airport'],
                $f['aircraft'],
                $f['flight_time'],
                $f['departure_time'],
                $f['landing_time'],
                $f['nickname'],
                $f['created_at']
            );
        }
        return $result;
    }

    public function getTotalFlightTimeByUserId(int $userId): string
    {
        $interval = $this->callFunction('get_total_flight_time', $userId);
        return substr($interval, 0, 5); // formatuje hh mm 
    }

    public function getMostUsedAirport(int $userId): ?string
    {
        return $this->callFunction('get_most_used_airport', $userId);
    }

    public function getMostUsedAircraft(int $userId): ?string
    {
        return $this->callFunction('get_most_used_aircraft', $userId);
    }

    private function callFunction(string $fn, int $userId): ?string
    {
        $stmt = $this->db->prepare("SELECT public.{$fn}(:uid)");
        $stmt->execute([':uid' => $userId]);
        $res = $stmt->fetchColumn();
        return $res ?: null;
    }
}
