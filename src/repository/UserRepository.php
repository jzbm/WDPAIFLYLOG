<?php

require_once __DIR__ . '/Database.php';

class UserRepository {
    private \PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->connect();
    }
    public function getAuthByEmail(string $email): ?array {
        $stmt = $this->db->prepare('
            SELECT * 
              FROM auth 
             WHERE email = :email
        ');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function createUser(string $nickname, int $roleId = 1): int {
        $stmt = $this->db->prepare('
            INSERT INTO users (nickname, role_id)
            VALUES (:nickname, :role_id)
            RETURNING id
        ');
        $stmt->execute([':nickname' => $nickname, ':role_id' => $roleId]);
        $userId = (int)$stmt->fetchColumn();
        return $userId;
    }
    public function createAuth(int $userId, string $email, string $hashedPassword): void {
        $this->db->prepare('
            INSERT INTO auth (id, email, password)
            VALUES (:id, :email, :password)
        ')->execute([
            ':id'       => $userId,
            ':email'    => $email,
            ':password' => $hashedPassword,
        ]);
    }

    public function getUserById(int $id): ?array {
        $stmt = $this->db->prepare('
            SELECT 
              u.id   AS id,
              a.email,
              u.nickname,
              r.name AS role,
              u.avatar
            FROM users u
            JOIN auth a ON a.id = u.id
            LEFT JOIN roles r ON r.id = u.role_id
           WHERE u.id = :id
        ');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function getAllUsersWithRoles(): array {
        return $this->db
            ->query('
                SELECT 
                  u.id AS id,
                  a.email,
                  u.nickname,
                  r.name AS role
                FROM users u
                JOIN auth a ON a.id = u.id
                LEFT JOIN roles r ON r.id = u.role_id
                ORDER BY u.id
            ')
            ->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getUsersByNickname(string $nickname): array {
        $stmt = $this->db->prepare('
            SELECT 
              u.id AS id,
              a.email,
              u.nickname,
              r.name AS role
            FROM users u
            JOIN auth a ON a.id = u.id
            LEFT JOIN roles r ON r.id = u.role_id
           WHERE u.nickname ILIKE :nick
           ORDER BY u.nickname
        ');
        $stmt->execute([':nick' => "%{$nickname}%"]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function deleteUserById(int $id): void {
        $this->db->prepare('
            DELETE FROM users
             WHERE id = :id
        ')->execute([':id' => $id]);
    }

    public function updateUserAvatar(int $userId, string $avatarPath): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE users
                SET avatar = :avatar
              WHERE id     = :id'
        );
        return $stmt->execute([
            ':avatar' => $avatarPath,
            ':id'     => $userId
        ]);
    }
}
