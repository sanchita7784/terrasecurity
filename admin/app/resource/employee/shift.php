<?php

use App\Form;
use App\Model\EmployeeShift;
use HardeepVicky\QueryBuilder\QuerySelect;
use HardeepVicky\QueryBuilder\Table;

require_once './app/model/BaseModel.php';
require_once './app/model/Employee.php';
require_once './app/model/EmployeeShift.php';
require_once './app/include/Form.php';

$empModel = new App\Model\Employee();

if (!isset($_GET['employee_id']))
{
    die("employee_id is required in GET");
}

$employee = $empModel->find([] , ["id" => $_GET['employee_id']]);

if (!$employee)
{
    die("Wrong Employee Id");
}
$employee = $employee[0];

$model = new EmployeeShift();

$form = new Form($model);

if (isset($_POST['form_data']))
{
    $apply_date = DateUtility::getDate($_POST['form_data']['apply_date'], "Y-m-d");

    $qs = new QuerySelect(new Table($model->getTable()));
    $qs->addRawWhere("apply_date > '$apply_date' and employee_id = " . $_GET['employee_id']);
    $qs->order("id", "DESC");
    $prev_record = $model->findQuery($qs);

    if ($prev_record)
    {
        $model->dateFields($prev_record);
        $prev_record = $prev_record[0];
        Session::writeFlash("fail", "Apply Date can not less than previous date : " . $prev_record['apply_date']);
    }
    else
    {
        $_POST['form_data']['employee_id'] = $_GET['employee_id'];
        if ($model->insert($_POST['form_data']))
        {
            Session::writeFlash("success", "Record has been Saved");
            redirect("employee/shift", ["employee_id" => $_GET['employee_id']]);
        }
        else
        {
            Session::writeFlash("fail", "Fail To Save");
        }
    }
}

$records = $model->find([],["employee_id" => $_GET['employee_id']], 'id', 'DESC');
$model->created_by($records);
$model->dateFields($records);
$model->shiftHours($records);

require_once './app/resource/layout/main/head.php'
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Shift Form</h4>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-lg-4">
                        <form method="post">
                            <div class="mb-3">
                                <b>Emp : </b> <?= $employee['name'] ?>
                                <br/><br/>
                                <b>M : </b> <?= $employee['mobile'] ?>
                            </div>     
                            <div class="mb-3">
                                <?= $form->label("apply_date", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("apply_date", ["class" => "form-control date-picker",
                                        "required" => true
                                ]); ?>
                            </div>                       
                            <div class="mb-3">
                                <?= $form->label("start_time", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("start_time", ["class" => "form-control time-picker",
                                        "required" => true
                                ]); ?>
                            </div>
                            <div class="mb-3">
                                <?= $form->label("end_time", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("end_time", ["class" => "form-control time-picker",
                                        "required" => true
                                ]); ?>
                            </div>                            
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary w-md">Submit</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-8">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Apply Date</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Hours</th>
                                    <th>Created</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($records as $i => $record):?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= $record['apply_date'] ?></td>
                                    <td><?= $record['start_time'] ?></td>
                                    <td><?= $record['end_time'] ?></td>
                                    <td><?= $record['hours'] ?> H</td>
                                    <td>
                                        <?=  DateUtility::getDate($record['created_at'], DateUtility::DATETIME_OUT_FORMAT) ?>
                                    </td>
                                    <td><?= $record['created_by']['name'] . '-' . $record['created_by']['id'] ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-danger confirm" data-msg="Are you sure to delete?" href="<?= url("employee/shift_delete", ["id" => $record['id'], "employee_id" => $record['employee_id']  ]) ?>">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
</div>


<?php require_once './app/resource/layout/main/foot.php' ?>