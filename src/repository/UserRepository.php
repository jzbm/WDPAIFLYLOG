<?php

require_once __DIR__ . '/Database.php';

class UserRepository {
    private $database;

    public function __construct() {
        $this->database = Database::getInstance()->connect();
    }

    
    public function getAuthByEmail(string $email): ?array {
        $stmt = $this->database->prepare('
            SELECT * FROM auth WHERE email = :email
        ');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    
    public function createAuth(string $email, string $hashedPassword): int {
        $stmt = $this->database->prepare('
            INSERT INTO auth (email, password) VALUES (:email, :password)
        ');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->execute();

        return $this->database->lastInsertId();
    }

    public function createUser(int $id, string $nickname): void {
        $stmt = $this->database->prepare('
            INSERT INTO users (id, nickname, role_id)
            VALUES (:id, :nickname, 1) -- 1 = USER
        ');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nickname', $nickname, PDO::PARAM_STR);
        $stmt->execute();
    }
    

    
    public function getUserById(int $id): ?array {
        $stmt = $this->database->prepare('
            SELECT * FROM users WHERE id = :id
        ');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getUserRoleById($id) {
        $stmt = $this->database->prepare('
            SELECT r.name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.id = :id
        ');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return strtolower($result['name']);  
    }
    

    
    public function getAllUsers(): array {
        $stmt = $this->database->prepare('
            SELECT id, nickname FROM users
        ');
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUsersWithRoles(): array {
        $stmt = $this->database->prepare('
            SELECT u.id, a.email, u.nickname, r.name AS role
            FROM users u
            JOIN auth a ON u.id = a.id
            JOIN roles r ON u.role_id = r.id
        ');
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteUserById(int $id): void {
        // users cassdcade db 
        $stmt = $this->database->prepare('DELETE FROM users WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        // auth
        $stmt = $this->database->prepare('DELETE FROM auth WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
    

    public function getUsersByNickname(string $nickname): array {
        $stmt = $this->database->prepare(
            'SELECT u.id, a.email, u.nickname, r.name as role 
             FROM users u 
             JOIN auth a ON u.id = a.id 
             JOIN roles r ON u.role_id = r.id 
             WHERE u.nickname LIKE :nickname'
        );
        $like = '%' . $nickname . '%';
        $stmt->bindParam(':nickname', $like, PDO::PARAM_STR);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }    
    
}
