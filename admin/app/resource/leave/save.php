<?php

use App\Form;
use App\Model\Leaves;

require_once './app/model/Leaves.php';
require_once './app/model/Employee.php';

$model = new Leaves();

$form = new Form($model);

$employee = new App\Model\Employee();

$records = $employee->find(["id", "name", "mobile"]);
$employee_list = [];

foreach($records as $record)
{
    $employee_list[$record['id']] = $record['name'] . " (" . $record['mobile'] . ")";
}

if (isset($_POST['form_data']))
{
    if (isset($_GET['id']))
    {
        $model->id = $_GET['id'];
        if ($model->update($_POST['form_data']))
        {
            Session::writeFlash("success", "Record has been updated.");
            redirect("leave/summary");
        }
        else
        {
            Session::writeFlash("fail", "Fail To Update.");
        }
    }
    else
    {
        if ($model->insert($_POST['form_data']))
        {
            Session::writeFlash("success", "Record has been Saved");
            redirect("leave/summary");
        }
        else
        {
            Session::writeFlash("fail", "Fail To Save");
        }
    }
}

require_once './app/resource/layout/main/head.php' 
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Leave Form</h4>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-lg-6">
                        <form method="post">                           
                            <div class="mb-3">
                                <?= $form->label("employee", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("employee_id", ["class" => "form-control select2", 
                                    "type" => "select",
                                    "list" => $employee_list,
                                    "empty" => true,
                                    "required" => true
                                ]); ?>
                            </div>  
                            <div class="mb-3">
                                <?= $form->label("date", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("date", [
                                    "class" => "form-control date-picker",
                                    "required" => true
                                ]); ?>
                            </div>                                                   
                            <div class="mb-3">
                                <?= $form->label("type", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("type", ["class" => "form-control select2", 
                                    "type" => "select",
                                    "list" => Leaves::TYPE_LIST,
                                    "empty" => true,
                                    "required" => true
                                ]); ?>
                            </div>                                                   
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary w-md">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
</div>

<?php require_once './app/resource/layout/main/foot.php' ?>