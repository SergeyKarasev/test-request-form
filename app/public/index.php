<?php

use App\App;

require_once "../vendor/autoload.php";

try {
    App::run();
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo $e->getMessage();
    die();
}
