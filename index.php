<?php

require 'Routing.php';
require_once __DIR__ . '/src/controllers/DefaultController.php';
require_once __DIR__ . '/src/controllers/PostController.php';
require_once __DIR__ . '/src/controllers/FlightController.php';
require_once __DIR__ . '/src/controllers/SecurityController.php';
require_once __DIR__ . '/src/controllers/MessageController.php';
require_once __DIR__ . '/src/controllers/NotificationController.php';
require_once __DIR__ . '/src/controllers/CommentController.php';
require_once __DIR__ . '/src/controllers/AdminController.php';
require_once __DIR__ . '/src/controllers/ProfileController.php';


//  mainpage domyslnie
$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);
if ($path === '') {
    $path = 'dashboard';
}

Routing::get('login', 'SecurityController');
Routing::post('login', 'SecurityController');
Routing::get('logout', 'SecurityController');
Routing::get('register', 'SecurityController');
Routing::post('register', 'SecurityController');

Routing::get('index', 'DefaultController');

Routing::get('dashboard', 'PostController');
Routing::get('posts', 'PostController');
Routing::post('add-post', 'PostController');
Routing::post('like-post', 'PostController');
Routing::post('delete-post', 'PostController');

Routing::post('add-flight', 'FlightController');
Routing::post('delete-flight', 'FlightController');

Routing::get('profile', 'ProfileController');
Routing::post('upload-avatar', 'ProfileController');

Routing::get('messages', 'MessageController');
Routing::post('send-message', 'MessageController');

Routing::get('notifications', 'NotificationController');
Routing::get('mark-all-as-read', 'NotificationController');

Routing::post('add-comment', 'CommentController');
Routing::post('add-comment-ajax', 'CommentController');

Routing::get('user-management', 'AdminController');
Routing::get('delete-user', 'AdminController');

Routing::get('get-messages-ajax', 'MessageController');


Routing::run($path);
