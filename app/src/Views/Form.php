<?php

namespace App\Views;

class Form extends View
{
    public function showBody(): void
    {
        require_once __DIR__ . '/../templates/form.php';
    }
}