<?php

class Comment {
    private $id;
    private $postId;
    private $userId;
    private $content;
    private $createdAt;
    private $nickname;

    public function __construct($id, $postId, $userId, $content, $createdAt, $nickname) {
        $this->id = $id;
        $this->postId = $postId;
        $this->userId = $userId;
        $this->content = $content;
        $this->createdAt = $createdAt;
        $this->nickname = $nickname;
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
}
