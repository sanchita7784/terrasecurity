<?php

require_once './app/model/BaseModel.php';
require_once './app/model/Role.php';

$model = new App\Model\Role();

if (isset($_GET['id']))
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
else
{
    Session::writeFlash("fail", "id not found in get");
}
   
redirect("role/summary");