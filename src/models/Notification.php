<?php

    class Notification {
        private $id;
        private $userId;
        private $content;
        private $isRead;
        private $createdAt;

        public function __construct($id, $userId, $content, $isRead, $createdAt) {
            $this->id = $id;
            $this->userId = $userId;
            $this->content = $content;
            $this->isRead = $isRead;
            $this->createdAt = $createdAt;
        }

        public function getId() {
            return $this->id;
        }

        public function getUserId() {
            return $this->userId;
        }

        public function getContent() {
            return $this->content;
        }

        public function isRead() {
            return $this->isRead;
        }

        public function getCreatedAt() {
            return $this->createdAt;
        }
    }
