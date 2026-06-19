<?php

require_once './app/model/EmployeeShift.php';

$model = new App\Model\EmployeeShift();

if (isset($_GET['id']))
{
    try
    {
        if ($model->delete($_GET['id']))
        {
            Session::writeFlash("success", "Shift has been deleted");
        }
        else
        {
            Session::writeFlash("fail", "Fail To delete");
        }

    }
    catch(\Exception $ex)
    {
        Session::writeFlash("fail", $ex->getMessage());
    }
}
else
{
    Session::writeFlash("fail", "id not found in get");
}
   
redirect("employee/shift", ["employee_id" => $_GET['employee_id']]);