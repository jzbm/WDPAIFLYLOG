<?php

class Flight {
    private $id;
    private $userId;
    private $departureAirport;
    private $landingAirport;
    private $aircraft;
    private $flightTime;
    private $departureTime;
    private $landingTime;
    private $nickname;
    private $createdAt;

    public function __construct($id, $userId, $departureAirport, $landingAirport, $aircraft, $flightTime, $departureTime, $landingTime, $nickname, $createdAt) {
        $this->id = $id;
        $this->userId = $userId;
        $this->departureAirport = $departureAirport;
        $this->landingAirport = $landingAirport;
        $this->aircraft = $aircraft;
        $this->flightTime = $flightTime;
        $this->departureTime = $departureTime;
        $this->landingTime = $landingTime;
        $this->nickname = $nickname; //
        $this->createdAt = $createdAt;
    }

    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getDepartureAirport() {
        return $this->departureAirport;
    }

    public function getLandingAirport() {
        return $this->landingAirport;
    }

    public function getAircraft() {
        return $this->aircraft;
    }

    public function getFlightTime() {
        return $this->flightTime;
    }

    public function getDepartureTime() {
        return $this->departureTime;
    }

    public function getLandingTime() {
        return $this->landingTime;
    }

    public function getNickname() {
        return $this->nickname; 
    }

    public function getCreatedAt() {
        return $this->createdAt; 
    }
}
