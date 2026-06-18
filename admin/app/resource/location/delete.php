<?php

require_once './app/model/Location.php';

$model = new App\Model\Location();

if (isset($_GET['id']))
{
    try
    {
        if ($model->delete($_GET['id']))
        {
            Session::writeFlash("success", "Location has been deleted");
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
   
redirect("location/summary");