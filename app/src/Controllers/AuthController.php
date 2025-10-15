<?php

namespace App\Controllers;

use App\App;
use App\Router;
use App\Views\Auth;
use Exception;

class AuthController extends Controller
{
    private string $title = 'Authorization';
    private array $textFields = [
        'email' => ['placeholder' => 'E-mail', 'required' => true],
        'password' => ['placeholder' => 'Password', 'password' => true, 'required' => true],
    ];

    protected function post(): string
    {
        $this->authorization($_POST);
        return array_search(AuthController::class, App::getRouter()->routes);
    }

    protected function get(): void
    {
        if (self::isAuth()) {
            $location = array_search(EmployeeController::class, App::getRouter()->routes);
            Router::redirect($location);
        }
        $view = new Auth($this->title);
        $view->setVar('formLink', 'Installation request form');
        $view->setVar('textFields', $this->textFields);
        $view->setVar('note', 'x@gmail.com или y@gmail.com | пароль любой не пустой');
        $view->setVar('authNotice', $_SESSION['authNotice'] ?? []);
        unset($_SESSION['authNotice']);
        $view->show();
    }

    private function authorization(array $request): void
    {
        try {
            $employee = [];
            foreach ($this->textFields as $fieldName => $field) {
                $employee[$fieldName] = $request[$fieldName] ?? '';
                if (!empty($field['required']) && empty($employee[$fieldName])) {
                    $placeholder = $field['placeholder'] ?? $fieldName;
                    throw new Exception("Empty required field: {$placeholder}.");
                }
            }
            $employeeEmails = App::getDb()->query('SELECT email FROM employees WHERE is_active = 1');
            $employeeEmails = array_column($employeeEmails, 'email');
            if (!in_array($employee['email'], $employeeEmails) || empty($employee['password'])) {
                throw new Exception('Your email or password is incorrect.');
            }
            $employee = App::getDb()->query("SELECT * FROM employees WHERE email = '{$employee['email']}'");;
            $_SESSION['auth'] = reset($employee);
        } catch (Exception $e) {
            $_SESSION['authNotice'] = [
                'type' => 'error',
                'text' => "Authorization failed. {$e->getMessage()}",
            ];
        }
    }

    public static function isAuth(): bool
    {
        $employeeId = $_SESSION['auth']['employee_id'] ?? 0;
        $employeeIds = App::getDb()->query('SELECT employee_id FROM employees WHERE is_active = 1');
        $employeeIds = array_column($employeeIds, 'employee_id');
        return in_array($employeeId, $employeeIds);
    }

    public static function unsetAuth(): void
    {
        unset($_SESSION['auth']);
    }
}