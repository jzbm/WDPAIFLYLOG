<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/UserRepository.php';
require_once __DIR__ . '/../repository/PostRepository.php';
require_once __DIR__ . '/../repository/CommentRepository.php'; // ✅ Dodano repozytorium komentarzy

class DefaultController extends AppController {
    private $userRepository;
    private $postRepository;
    private $commentRepository; // ✅ Nowa zmienna

    public function __construct() {
        parent::__construct();
        $this->userRepository = new UserRepository();
        $this->postRepository = new PostRepository();
        $this->commentRepository = new CommentRepository(); // ✅ Inicjalizacja repozytorium komentarzy
    }

    public function index() {
        $this->dashboard(); // Teraz dashboard otwiera się domyślnie
    }

    // ✅ Dashboard dostępny dla wszystkich
    public function dashboard() {
        $posts = $this->postRepository->get_posts(); 

        // ✅ Pobranie komentarzy dla każdego posta
        foreach ($posts as $post) {
            $comments = $this->commentRepository->getCommentsByPostId($post->getId());
            $post->setComments($comments); // ✅ Ustawianie komentarzy w modelu posta
        }

        $this->render('dashboard', [
            'posts' => $posts,
            'commentRepository' => $this->commentRepository // ✅ Przekazanie do widoku
        ]);
    }
}
