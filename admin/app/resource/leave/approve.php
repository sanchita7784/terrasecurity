<?php

use App\Model\Leaves;

require_once './app/model/Leaves.php';

$model = new App\Model\Leaves();

if (isset($_GET['id']))
{
    $record = $model->find(["status"] , ["id" => $_GET['id']]);
    if (!$record)
    {
        die("Wrong Id");
    }
    $record = $record[0];

    $model->id = $_GET['id'];
    if ($model->update(["status" => Leaves::APPROVE]) )
    {
        ?>
        <span class="badge bg-success">Approved</span>
        <?php
    }
    else
    {
        die("Fail To Update Record");
    }
}
else
{
    die("id not found in get");
}