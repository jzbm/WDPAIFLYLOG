<?php

class Post {
    private $id;
    private $userId;
    private $title;
    private $content;
    private $nickname;
    private $image;
    private $likesCount;
    private $isLikedByUser;
    private $createdAt;

    public function __construct($id, $userId, $title, $createdAt, $content, $nickname, $image = null) {
        $this->id = $id;
        $this->userId = $userId;
        $this->title = $title;
        $this->content = $content;
        $this->nickname = $nickname;
        $this->image = $image;
        $this->createdAt = $createdAt;
    }

    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getFormattedContent() {
        return nl2br(htmlspecialchars_decode($this->content));
    }

    public function getNickname() {
        return $this->nickname;
    }
    
    public function getImage() {
        return $this->image;
    }
    public function setComments(array $comments) {
        $this->comments = $comments;
    }
    
    public function getComments() {
        return $this->comments;
    }

    public function setLikesCount($likesCount) {
        $this->likesCount = $likesCount;
    }
    
    public function getLikesCount() {
        return $this->likesCount;
    }
    
    public function setIsLikedByUser($isLiked) {
        $this->isLikedByUser = $isLiked;
    }
    
    public function isLikedByUser() {
        return $this->isLikedByUser;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }
}
