<?php

namespace App\Views;

class ErrorPage extends View
{
    public function showBody(): void
    {
        require_once __DIR__ . '/../templates/404.php';
    }
}