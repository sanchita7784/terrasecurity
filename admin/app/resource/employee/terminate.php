<?php

use App\Form;

require_once './app/model/BaseModel.php';
require_once './app/model/Employee.php';
require_once './app/model/EmployeeWorkHistory.php';
require_once './app/include/Form.php';

$employee = new App\Model\Employee();

$employee_record = $employee->find([], ["id" => $_GET['employee_id']]);
if (!$employee_record)
{
    die("Wrong Employee ID");
}
$employee_record = $employee_record[0];

$doj_date = DateUtility::change($employee_record['doj'], 1, DateUtility::DAYS, DateUtility::DATE_FORMAT);
$doj_days = DateUtility::diff($doj_date, date("Y-m-d"), DateUtility::DAYS);

if ($employee_record['is_terminate'])
{
    die("Employee is already terminated");
}

$empWorkHistory = new App\Model\EmployeeWorkHistory();

$form = new Form($employee);

if (isset($_POST['form_data']))
{
    $save = $_POST['form_data'];

    if (DateUtility::compare($save['terminate_date'], date("Y-m-d")) <= 0)
    {
        $save['is_terminate'] = 1; 
        $save['is_re_join'] = 0; 
    }

    if (isset($_GET['employee_id']))
    {
        $employee->id = $_GET['employee_id'];
        if ($employee->update($save))
        {
            $save = [
                "employee_id" => $employee_record['id'],
                "start_date" => $employee_record['doj'],
                "end_date" => $_POST['form_data']['terminate_date'],   
                "leave_reason" => $_POST['form_data']['leave_reason'],   
            ];

            if ($empWorkHistory->insert($save))
            {
                Session::writeFlash("success", "Employee has been updated.");
                redirect("employee/summary");
            }
            else
            {
                Session::writeFlash("fail", "Fail To Save.");    
            }
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
                <h4 class="card-title">Employee Terminate Form</h4>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-lg-6">
                        <form method="post">
                            <div class="mb-3">
                                <?= $form->label("terminate_date", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("terminate_date", ["class" => "form-control date-picker", 
                                    "required" => true,
                                    "data-date-start" => $doj_days
                                ]);?>
                            </div>
                            <div class="mb-3">
                                <?= $form->label("leave_reason", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("leave_reason", ["class" => "form-control", "required" => true]); ?>
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