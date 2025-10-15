<?php

namespace App\Views;

class Auth extends View
{
    public function showBody(): void
    {
        require_once __DIR__ . '/../templates/auth.php';
    }
}