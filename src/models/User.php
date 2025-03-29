<?php

class User {
    private $id;
    private $email;
    private $password;
    private $nickname;
    private $roleId;
    private $avatar;

    public function __construct(
        int $id,
        string $email,
        string $password,
        string $nickname,
        int $roleId,
        string $avatar = null
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->nickname = $nickname;
        $this->roleId = $roleId;
        $this->avatar = $avatar;
    }

  
    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getNickname(): string
    {
        return $this->nickname;
    }

    public function getRoleId(): int
    {
        return $this->roleId;
    }

    public function getAvatar(): string {
        if (empty($this->avatar)) {
            return '/uploads/avatars/default.png';
        }
    
        return str_contains($this->avatar, '/') 
            ? '/' . ltrim($this->avatar, '/') 
            : '/uploads/avatars/' . $this->avatar;
    }
    

    
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setNickname(string $nickname): void
    {
        $this->nickname = $nickname;
    }

    public function setRoleId(int $roleId): void
    {
        $this->roleId = $roleId;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }
}
