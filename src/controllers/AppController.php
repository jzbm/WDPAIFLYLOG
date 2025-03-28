<?php

class AppController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function render(string $template = null, array $variables = []) {
        $templatePath = 'public/views/' . $template . '.html';

        if (file_exists($templatePath)) {
            extract($variables);
            include $templatePath;
        }
    }
}
