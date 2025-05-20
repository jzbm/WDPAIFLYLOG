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

    public function createUser(string $nickname): int {
        $stmt = $this->db->prepare('
            INSERT INTO users (nickname)
            VALUES (:nickname)
            RETURNING id
        ');
        $stmt->execute([':nickname' => $nickname]);
        $userId = (int)$stmt->fetchColumn();

        $this->db->prepare('
            INSERT INTO user_roles (user_id, role_id)
            VALUES (:uid, 1)
        ')->execute([':uid' => $userId]);

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
              user_id   AS id,
              email,
              nickname,
              role_name AS role,
              avatar
            FROM v_users_with_roles
           WHERE user_id = :id
        ');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function getAllUsersWithRoles(): array {
        return $this->db
            ->query('
                SELECT 
                  user_id AS id,
                  email,
                  nickname,
                  role_name AS role
                  -- możesz też dodać avatar, jeśli potrzebujesz
                FROM v_users_with_roles
                ORDER BY user_id
            ')
            ->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getUsersByNickname(string $nickname): array {
        $stmt = $this->db->prepare('
            SELECT 
              user_id AS id,
              email,
              nickname,
              role_name AS role
            FROM v_users_with_roles
           WHERE nickname ILIKE :nick
           ORDER BY nickname
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
}
