<?php

namespace App\Views;

class Schedule extends View
{
    public function showBody(): void
    {
        require_once __DIR__ . '/../templates/schedule.php';
    }
}