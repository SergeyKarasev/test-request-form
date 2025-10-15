<?php

namespace App\Controllers;

use App\App;
use App\Views\ErrorPage;

class ErrorPageController extends Controller
{
    private string $title = '404 Not Found';

    protected function post(): string
    {
        return array_search(FormController::class, App::getRouter()->routes);
    }

    protected function get(): void
    {
        $view = new ErrorPage($this->title);
        $view->setVar('indexLink', 'Go to software installation request form');
        $view->show();
    }
}