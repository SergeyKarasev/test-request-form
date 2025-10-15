<?php

namespace App\Controllers;

use App\App;
use App\Router;
use App\Views\Schedule;

class EmployeeController extends Controller
{
    private string $title = 'Employee schedule';
    protected function post(): string
    {
        AuthController::unsetAuth();
        return array_search(EmployeeController::class, App::getRouter()->routes);
    }

    protected function get(): void
    {
        if (!AuthController::isAuth()) {
            $location = array_search(AuthController::class, App::getRouter()->routes);
            Router::redirect($location);
        }
        $employee = $_SESSION['auth'];
        $view = new Schedule($this->title);
        $view->setVar('formLink', 'Installation request form');
        $view->setVar('installationRequests', $this->getInstallationRequests($employee['employee_id']));
        $view->setVar('employee', $employee);
        $view->show();
    }

    private function getInstallationRequests($employeeId): array
    {
        $sql = "SELECT * FROM installation_requests LEFT JOIN software USING (software_id) WHERE employee_id = {$employeeId} ORDER BY time_from DESC";
        $installationRequests = [];
        foreach (App::getDb()->query($sql) as $request) {
            $note = empty(trim($request['company'] ?? '')) ? '' : ' Company: ' . trim($request['company']);
            $note .= empty(trim($request['contact'] ?? '')) ? '' : ' Contact: ' . trim($request['contact']);
            $note .= empty(trim($request['phone'] ?? '')) ? '' : ' Phone: ' . trim($request['phone']);
            $note .= empty(trim($request['email'] ?? '')) ? '' : ' E-mail: ' . trim($request['email']);
            $installationRequests[] = [
                'date' => date('D, d M H:i', $request['time_from'] ?? 0) . date(' - H:i', $request['time_to'] ?? 0),
                'software' => $request['software'] ?? '',
                'note' => $note,
            ];
        }
        return $installationRequests;
    }
}