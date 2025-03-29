<?php

class Comment {
    private $id;
    private $postId;
    private $userId;
    private $content;
    private $createdAt;
    private $nickname;
    private $avatar;

    public function __construct($id, $postId, $userId, $content, $createdAt, $nickname, $avatar) {
        $this->id = $id;
        $this->postId = $postId;
        $this->userId = $userId;
        $this->content = $content;
        $this->createdAt = $createdAt;
        $this->nickname = $nickname;
        $this->avatar = $avatar;
    }

    public function getId() {
        return $this->id;
    }

    public function getPostId() {
        return $this->postId;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getContent() {
        return $this->content;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function getNickname() {
        return $this->nickname;
    }
    

    public function getAvatar(): string {
        
        if (empty($this->avatar)) {
            return '/uploads/avatars/default.png';
        }
        if (str_contains($this->avatar, '/')) {
            return '/' . ltrim($this->avatar, '/'); 
        }
    
        return '/uploads/avatars/' . $this->avatar;
    }
    
    
}
