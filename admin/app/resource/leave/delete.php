<?php

require_once './app/model/Company.php';

$model = new App\Model\Company();

if (isset($_GET['id']))
{
    try
    {
        if ($model->delete($_GET['id']))
        {
            Session::writeFlash("success", "Leave has been deleted");
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
   
redirect("company/summary");