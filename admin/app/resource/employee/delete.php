<?php

require_once './app/model/Employee.php';

$model = new App\Model\Employee();

if (isset($_GET['id']))
{
    try
    {
        if ($model->delete($_GET['id']))
        {
            Session::writeFlash("success", "Employee has been deleted");
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
   
redirect("employee/summary");