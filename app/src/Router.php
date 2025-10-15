<?php

namespace App;

use App\Controllers\Controller;
use App\Controllers\ErrorPageController;

class Router
{
    private Controller $controller;

    public function __construct(
        readonly array $routes,
        readonly string $uri
    )
    {
        if (isset($this->routes[$this->uri])) {
            $this->controller = new $this->routes[$this->uri]();
        } else {
            $this->controller = new ErrorPageController();
        }
    }

    public function runController(): void
    {
        $this->controller->index();
    }

    public static function redirect($location): void
    {
        if (headers_sent()) {
            echo '<meta http-equiv="refresh" content="0;url=' . $location . '" />';
        } else {
            header('Location: ' . $location);
        }
        die();
    }
}