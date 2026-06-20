<?php

use App\Form;

require_once './app/model/BaseModel.php';
require_once './app/model/Employee.php';
require_once './app/include/Form.php';

$employee = new App\Model\Employee();

$employee_record = $employee->find([], ["id" => $_GET['employee_id']]);
if (!$employee_record)
{
    die("Wrong Employee ID");
}
$employee_record = $employee_record[0];

if (!$employee_record['terminate_date'])
{
    die("Employee is not terminated");
}

if ($employee_record['is_re_join'])
{
    die("Employee is already joined");
}

$terminate_date = DateUtility::change($employee_record['terminate_date'], 1, DateUtility::DAYS, DateUtility::DATE_FORMAT);
$terminate_days = DateUtility::diff($terminate_date, date("Y-m-d"), DateUtility::DAYS);

if ($employee_record['is_re_join'])
{
    die("Employee is already joined");
}

$form = new Form($employee);

if (isset($_POST['form_data']))
{
    $save = $_POST['form_data'];
    $save['is_re_join'] = 1;
    $save['is_terminate'] = 0;
    $save['terminate_date'] = null;

    if (isset($_GET['employee_id']))
    {
        $employee->id = $_GET['employee_id'];
        if ($employee->update($save))
        {
            Session::writeFlash("success", "Employee has been updated.");
            redirect("employee/summary");
        }
        else
        {
            Session::writeFlash("fail", "Fail To Update.");
        }
    }
    else
    {
        die("Employee ID is required");
    }
}

require_once './app/resource/layout/main/head.php'
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Employee Re-Join Form</h4>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-lg-6">
                        <form method="post">
                            <div class="mb-3">
                                <?= $form->label("rejoin date", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("doj", ["class" => "form-control date-picker", 
                                    "required" => true,
                                    "data-date-start" => $terminate_days
                                ]);?>
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