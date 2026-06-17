<?php
require_once './app/model/Employee.php';

$model = new App\Model\Employee();

$response = ['status' => 1, "msg" => "", "data" => []];

$records = $model->find([], ["id" => $_GET['emp_id']]);

if ($records)
{
    $response['data']['employee'] = $records[0];
}

echo json_encode($response);

