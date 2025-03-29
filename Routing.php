<?php

require_once 'src/controllers/DefaultController.php';
require_once 'src/controllers/PostController.php';
require_once 'src/controllers/SecurityController.php';
require_once 'src/controllers/FlightController.php';
require_once 'src/controllers/MessageController.php';
require_once 'src/controllers/NotificationController.php';
require_once 'src/controllers/ProfileController.php';

class Routing {
    public static $routes = [];

    public static function get($url, $controller) {
        self::$routes[$url] = $controller;
    }

    public static function post($url, $controller) {
        self::$routes[$url] = $controller;
    }

    public static function run($url) {
        $action = explode("/", $url)[0];
    
        if (!array_key_exists($action, self::$routes)) {
            die("Wrong URL!");
        }
    
        $controller = self::$routes[$action];
        $object = new $controller;
    
        // snake_case 
        $method = str_replace('-', '_', $action); 
    
        if (method_exists($object, $method)) {
            $object->$method(); 
        } else {
            die("Action '$method' not found in controller!");
        }
    }
}
