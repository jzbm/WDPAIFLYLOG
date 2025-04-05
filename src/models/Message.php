<?php 

    class Message {
        private $id;
        private $senderId;
        private $receiverId;
        private $content;
        private $createdAt;

    public function __construct($id, $senderId, $receiverId, $content, $createdAt) {
        $this->id = $id;
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->content = $content;
        $this->createdAt = $createdAt;
    }

    public function getId() {
        return $this->id;
    }

    public function getSenderId() {
        return $this->senderId;
    }

    public function getReceiverId() {
        return $this->receiverId;
    }

    public function getContent() {
        return $this->content;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }
}
