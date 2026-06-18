<?php
require_once './app/model/Employee.php';
require_once './app/model/Attendance.php';

$employee = new App\Model\Employee();

$response = ['status' => 1, "msg" => "", "data" => []];

$records = $employee->find([], ["id" => $_GET['emp_id']]);

if ($records)
{
    $response['data']['employee'] = $records[0];
}

$attendance = new App\Model\Attendance();

$records = $attendance->find([], ["employee_id" => $_GET['emp_id']], "id", "desc", 0, 1);

if ($records)
{
    $attendance->dateFields($records);

    $last_record = $records[0];

    if ($last_record['out_time'])
    {   
        $response['data']['last_attendance'] = "Out : " .  $last_record['out_time'];
    }    
    else if ($last_record['in_time'])
    {
        $response['data']['last_attendance'] = "In : " .  $last_record['in_time'];
    }
}

echo json_encode($response);

