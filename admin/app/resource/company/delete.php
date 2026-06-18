<?php

require_once './app/model/Company.php';

$model = new App\Model\Company();

if (isset($_GET['id']))
{
    if ($model->delete($_GET['id']))
    {
        Session::writeFlash("success", "Company has been deleted");
    }
    else
    {
        Session::writeFlash("fail", "Fail To delete, Record has associated data");
    }
}
else
{
    Session::writeFlash("fail", "id not found in get");
}
   
redirect("company/summary");