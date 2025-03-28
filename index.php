<?php

require 'Routing.php';
require_once __DIR__ . '/src/controllers/DefaultController.php';
require_once __DIR__ . '/src/controllers/PostController.php';
require_once __DIR__ . '/src/controllers/FlightController.php';
require_once __DIR__ . '/src/controllers/SecurityController.php';
require_once __DIR__ . '/src/controllers/MessageController.php';
require_once __DIR__ . '/src/controllers/NotificationController.php';
require_once __DIR__ . '/src/controllers/CommentController.php';
require_once __DIR__ . '/src/controllers/DashboardController.php';

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);

if ($path === '') {
    $path = 'dashboard';
}

// ✅ Routing dla SecurityController
Routing::get('login', 'SecurityController');
Routing::post('login', 'SecurityController');
Routing::get('logout', 'SecurityController');
Routing::get('register', 'SecurityController');
Routing::post('register', 'SecurityController');

// ✅ Routing dla DefaultController
Routing::get('index', 'DefaultController');
Routing::get('dashboard', 'DefaultController');

// ✅ Routing dla PostController
Routing::get('posts', 'PostController');
Routing::post('add-post', 'PostController'); 

// ✅ Routing dla FlightController
Routing::post('add-flight', 'FlightController');
Routing::post('delete-flight', 'FlightController');
Routing::get('profile', 'FlightController');
Routing::post('upload-avatar', 'FlightController');

Routing::get('messages', 'MessageController');
Routing::post('send-message', 'MessageController');

Routing::get('notifications', 'NotificationController');

Routing::post('add-comment', 'CommentController');

Routing::post('like-post', 'DashboardController');

Routing::run($path);
