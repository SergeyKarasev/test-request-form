<?php

namespace App;

use App\Controllers\AuthController;
use App\Controllers\FormController;
use App\Controllers\EmployeeController;
use ErrorException;

class App
{
    const SCHEDULE_FOR_DAYS = 3;
    const START_WORK = '09:00';
    const END_WORK = '18:00';
    const DB_HOST = 'mysql';
    const DB_NAME = 'application_form';
    const DB_USER = 'root';
    const DB_PASSWORD = 'root';
    private static Database $db;
    private static Router $router;

    public static function run(): void
    {
        self::boot();
        self::$db->migrateDemo();
        self::$router->runController();
    }

    private static function boot(): void
    {
        session_start() or throw new ErrorException('Session start failed');
        date_default_timezone_set('Europe/Moscow');
        self::$db = new Database(
            host: self::DB_HOST,
            dbname: self::DB_NAME,
            username: self::DB_USER,
            password: self::DB_PASSWORD,
        );
        self::$router = new Router(
            routes: [
                '/' => FormController::class,
                '/index.php' => FormController::class,
                '/employee' => EmployeeController::class,
                '/employee/' => EmployeeController::class,
                '/auth' => AuthController::class,
                '/auth/' => AuthController::class,
            ],
            uri: $_SERVER['REQUEST_URI']
        );
    }

    public static function getDb(): Database
    {
        return self::$db;
    }

    public static function getRouter(): Router
    {
        return self::$router;
    }
}