<?php

namespace App\Controllers;

use App\App;
use App\Views\Form;
use DateTime;
use ErrorException;
use Exception;

class FormController extends Controller
{
    private string $title = 'Software Installation Request';
    private array $textFields = [
        'company' => ['placeholder' => 'Company'],
        'contact' => ['placeholder' => 'Contact', 'required' => true],
        'phone' => ['placeholder' => 'Phone', 'required' => true],
        'email' => ['placeholder' => 'E-mail'],
    ];

    protected function post():string
    {
        $this->saveInstallationRequest($_POST);
        return array_search(FormController::class, App::getRouter()->routes);
    }
    protected function get(): void
    {
        $view = new Form($this->title);
        $view->setVar('employeeLink', 'Employee schedule');
        $view->setVar('textFields', $this->textFields);
        $view->setVar('software', $this->getSoftware());
        $view->setVar('installationRequestNotice', $_SESSION['installationRequestNotice'] ?? []);
        unset($_SESSION['installationRequestNotice']);
        $view->show();
    }

    private function getSoftware(?int $softwareId = null, bool $onlyActive = true, bool $getTimeSlots = true, bool $mergeEmployeeTimeSlots = true): array
    {
        $sql_condition = $softwareId ? " AND software_id = {$softwareId}" : '';
        $sql_condition .= $onlyActive ? ' AND is_active = 1' : '';
        $software = App::getDb()->query("SELECT * FROM software WHERE 1=1 {$sql_condition}");
        $software = array_column($software, 'software', 'software_id');
        if ($getTimeSlots) {
            $_software = [];
            foreach ($software as $softwareId => $softwareName) {
                $_software[$softwareId]['softwareName'] = $softwareName;
            }
            $software = $this->setTimeSlots($_software);
        }
        if ($mergeEmployeeTimeSlots) {
            foreach ($software as &$_software) {
                if (empty($_software['timeSlots'])) {
                    continue;
                }
                $_software['timeSlots'] = $this->mergeEmployeeTimeSlots($_software['timeSlots']);
            }
        }
        return $software;
    }

    private function setTimeSlots(array $software): array
    {
        $sql_join = 'LEFT JOIN employees USING (employee_id)';
        $softwareIds = implode(',', array_keys($software));
        $sql_condition = "software_id IN ({$softwareIds})";
        $sql_condition .= ' AND is_active = 1';
        $softwareEmployees = App::getDb()->query("SELECT * FROM software_employees {$sql_join} WHERE {$sql_condition}");
        foreach ($softwareEmployees as $row) {
            $employeeTimeSlots = $this->generateTimeSlots("+{$row['minutes']} minutes");
            $employeeTimeSlots = $this->validateTimeSlots($row['employee_id'], $employeeTimeSlots);
            $software[$row['software_id']]['timeSlots'][$row['employee_id']] = $employeeTimeSlots;
        }
        return $software;
    }

    private function generateTimeSlots(string $modifier): array
    {
        $start = strtotime(App::START_WORK);
        $end = strtotime(App::END_WORK);
        if ($start < strtotime($modifier, $start) && strtotime($modifier, $start) <= $end) {
            $timeSlots = [];
            $time = new DateTime('yesterday');
            $endTime = clone $time;
            for ($i = 1; $i <= App::SCHEDULE_FOR_DAYS; $i++) {
                $time->modify('next weekday' . App::START_WORK);
                $endTime->modify('next weekday' . App::END_WORK);
                $_timeSlots = [];
                while ($time <= $endTime) {
                    $startTime = clone $time;
                    $time->modify($modifier);
                    if ($time > $endTime) {
                        continue;
                    }
                    $_timeSlots["{$startTime->format('H:i')} - {$time->format('H:i')}"] = [
                        $startTime->getTimestamp(),
                        $time->getTimestamp(),
                    ];
                }
                $timeSlots[$time->format('D, d M')] = $_timeSlots;
            }
        }
        return $timeSlots ?? [];
    }

    private function validateTimeSlots(int $employeeId, array $employeeTimeSlots): array
    {
        static $reservedTimeSlots;
        $time = time();
        if (!isset($reservedTimeSlots)) {
            $reservedTimeSlots = [];
            $sql = "SELECT employee_id, time_from, time_to FROM installation_requests WHERE time_from > {$time}";
            foreach (App::getDb()->query($sql) as $timeSlot) {
                $employeeId = $timeSlot['employee_id'];
                unset($timeSlot['employee_id']);
                $reservedTimeSlots[$employeeId][] = $timeSlot;
            }
        }
        $employeeReservedTimeSlots = $reservedTimeSlots[$employeeId] ?? [];
        $employeeReservedTimeSlots[] = ['time_from' => 0, 'time_to' => $time];
        foreach ($employeeTimeSlots as $strDate => $timeSlots) {
            foreach ($timeSlots as $strTime => $timeSlot) {
                $timeFrom1 = $timeSlot[0] ?? 0;
                $timeFrom1++;
                $timeTo1 = $timeSlot[1] ?? 0;
                $timeTo1--;
                foreach ($employeeReservedTimeSlots as $_timeSlot) {
                    $timeFrom2 = $_timeSlot['time_from'] ?? 0;
                    $timeTo2 = $_timeSlot['time_to'] ?? 0;
                    if ($timeFrom1 < $timeTo2 && $timeFrom2 < $timeTo1) {
                        $employeeTimeSlots[$strDate][$strTime]['invalid'] = true;
                    }
                }
            }
        }
        return $employeeTimeSlots;
    }

    private function mergeEmployeeTimeSlots(array $employeeTimeSlots): array
    {
        $mergedTimeSlots = [];
        foreach ($employeeTimeSlots as $timeSlots) {
            foreach ($timeSlots as $strDate => $dateSlots) {
                foreach ($dateSlots as $strTime => $timeSlot) {
                    $timeSlot['slot'] = implode('_', $timeSlot);
                    $mergedTimeSlots[$strDate][$strTime] = $timeSlot;
                }
                ksort($mergedTimeSlots[$strDate]);
            }
        }
        return $mergedTimeSlots;
    }

    private function saveInstallationRequest(array $request): void
    {
        try {
            $insert = [];
            foreach ($this->textFields as $fieldName => $field) {
                $insert[$fieldName] = $request[$fieldName] ?? '';
                if (!empty($field['required']) && empty($insert[$fieldName])) {
                    $placeholder = $field['placeholder'] ?? $fieldName;
                    throw new Exception("Empty required field: {$placeholder}.");
                }
            }

            $softwareId = intval($request['software'] ?? 0);
            $softwareIds = array_keys($this->getSoftware(getTimeSlots: false));
            if (in_array($softwareId, $softwareIds)) {
                $insert['software_id'] = $softwareId;
            } else {
                throw new Exception('The selected software is not available for installation.');
            }

            $selectedTimeSlot = $request['timeSlot'] ?? '';
            $selectedTimeSlot = explode('_', $selectedTimeSlot);
            $selectedTimeSlot = array_map('intval', $selectedTimeSlot);
            if (empty($selectedTimeSlot) || count($selectedTimeSlot) != 2) {
                throw new Exception('The selected time slot for software installation is not available.');
            }

            $software = $this->getSoftware(
                softwareId: $softwareId,
                mergeEmployeeTimeSlots: false
            );
            $software = reset($software);
            foreach ($software['timeSlots'] as $employeeId => $dateSlots) {
                foreach ($dateSlots as $strDate => $dateSlot) {
                    if (in_array($selectedTimeSlot, $dateSlot)) {
                        $insert['employee_id'] = $employeeId;
                        $insert['time_from'] = $selectedTimeSlot[0];
                        $insert['time_to'] = $selectedTimeSlot[1];
                        break 2;
                    }
                }
            }
            if (empty($insert['employee_id'])) {
                throw new Exception('The selected time slot for software installation is not available.');
            }

            $sql_columns = $sql_values = '';
            foreach ($insert as $column => $value) {
                $sql_columns .= " {$column},";
                $sql_values .= " '{$value}',";
            }
            $sql_columns = trim($sql_columns, ' ,');
            $sql_values = trim($sql_values, ' ,');

            App::getDb()->query("INSERT INTO installation_requests ({$sql_columns}) VALUES ({$sql_values})");
            $_SESSION['installationRequestNotice'] = [
                'type' => 'success',
                'text' => 'The installation request has been accepted and will be processed as soon as possible.',
            ];
        } catch (ErrorException $e) {
            throw new ErrorException($e->getMessage());
        } catch (Exception $e) {
            $_SESSION['installationRequestNotice'] = [
                'type' => 'error',
                'text' => "The installation request has not accepted. {$e->getMessage()}",
            ];
        }
    }
}