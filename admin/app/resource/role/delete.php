<?php

require_once './app/model/BaseModel.php';
require_once './app/model/Role.php';

$model = new App\Model\Role();

if (isset($_GET['id']))
{
    try
    {
        if ($model->delete($_GET['id']))
        {
            Session::writeFlash("success", "Role has been deleted");
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
   
redirect("role/summary");