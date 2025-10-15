<?php

namespace App\Controllers;

use App\App;
use ErrorException;

abstract class Controller
{
    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $location = $this->post();
            App::getRouter()->redirect($location);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->get();
        } else {
            throw new ErrorException('Bad request', 400);
        }
    }

    protected abstract function post(): string;
    protected abstract function get(): void;
}