<?php

require_once __DIR__ . '/Database.php';

class UserRepository {
    private $database;

    public function __construct() {
        $this->database = Database::getInstance()->connect();
    }

    // ✅ Pobiera dane logowania (auth) po emailu
    public function getAuthByEmail(string $email): ?array {
        $stmt = $this->database->prepare('
            SELECT * FROM auth WHERE email = :email
        ');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // ✅ Tworzy rekord w tabeli auth
    public function createAuth(string $email, string $hashedPassword): int {
        $stmt = $this->database->prepare('
            INSERT INTO auth (email, password) VALUES (:email, :password)
        ');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->execute();

        return $this->database->lastInsertId();
    }

    // ✅ Tworzy rekord w tabeli users (dane profilu)
    public function createUser(int $id, string $nickname): void {
        $stmt = $this->database->prepare('
            INSERT INTO users (id, nickname) VALUES (:id, :nickname)
        ');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nickname', $nickname, PDO::PARAM_STR);
        $stmt->execute();
    }

    // ✅ Pobiera dane użytkownika (profilowe) po ID
    public function getUserById(int $id): ?array {
        $stmt = $this->database->prepare('
            SELECT * FROM users WHERE id = :id
        ');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // ✅ Pobieranie roli po ID (jeśli przechowujesz role np. w user_roles)
    public function getUserRoleById(int $id): ?string {
        $stmt = $this->database->prepare('
            SELECT role_id FROM users WHERE id = :id
        ');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user['role_id'] ?? null;
    }

    // ✅ Lista użytkowników (np. do wysyłania wiadomości)
    public function getAllUsers(): array {
        $stmt = $this->database->prepare('
            SELECT id, nickname FROM users
        ');
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
