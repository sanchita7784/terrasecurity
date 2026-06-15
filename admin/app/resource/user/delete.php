<?php

require_once './app/model/BaseModel.php';
require_once './app/model/User.php';

$user = new App\Model\User();

if (isset($_GET['id']))
{
    if ($user->delete($_GET['id']))
    {
        Session::writeFlash("success", "User has been deleted");
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
   
redirect("user/summary");