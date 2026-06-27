<?php

require_once './app/model/Invoice.php';

$model = new App\Model\Invoice();

if (isset($_GET['id']))
{
    try
    {
        $mysql->query("START TRANSACTION;");

        if ($model->delete($_GET['id']))
        {
            $mysql->query("COMMIT;");
            Session::writeFlash("success", "Salary has been deleted");
        }
        else
        {
            $mysql->query("ROLLBACK;");
            Session::writeFlash("fail", "Fail To delete");
        }

    }
    catch(\Exception $ex)
    {
        $mysql->query("ROLLBACK;");
        Session::writeFlash("fail", $ex->getMessage());
    }
}
else
{
    Session::writeFlash("fail", "id not found in get");
}
   
redirect("invoice/summary");