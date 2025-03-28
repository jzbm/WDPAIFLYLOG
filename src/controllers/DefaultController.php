<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/UserRepository.php';
require_once __DIR__ . '/../repository/PostRepository.php';
require_once __DIR__ . '/../repository/CommentRepository.php'; 

class DefaultController extends AppController {
    private $userRepository;
    private $postRepository;
    private $commentRepository; 

    public function __construct() {
        parent::__construct();
        $this->userRepository = new UserRepository();
        $this->postRepository = new PostRepository();
        $this->commentRepository = new CommentRepository(); 
    }

    public function index() {
        $this->dashboard(); 
    }

    public function dashboard() {
        $posts = $this->postRepository->get_posts(); 

        foreach ($posts as $post) {
            $comments = $this->commentRepository->getCommentsByPostId($post->getId());
            $post->setComments($comments); 
        }

        $this->render('dashboard', [
            'posts' => $posts,
            'commentRepository' => $this->commentRepository 
        ]);
    }
}
