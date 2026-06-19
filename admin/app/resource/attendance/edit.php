<?php

require_once './app/model/Employee.php';
require_once './app/model/Location.php';
require_once './app/model/Attendance.php';
require_once './app/include/Form.php';

if (!isset($_GET['id']))
{
    die("id in get request required");
}


$model = new App\Model\Attendance();

$form = new App\Form($model);

$location = new App\Model\Location();

$location_record = $location->find([], ["id" => $form->db_data['location_id']]);
$location_record = $location_record[0];

$employee = new App\Model\Employee();

$employee_record = $employee->find([], ["id" => $form->db_data['employee_id']]);
$employee_record = $employee_record[0];


if (isset($_POST['form_data']))
{
    $id = $_GET['id'];
    $in_date = DateUtility::getDate($_POST['form_data']['in_time'], DateUtility::DATETIME_FORMAT);
    $out_time = DateUtility::getDate($_POST['form_data']['out_time'], DateUtility::DATETIME_FORMAT);

    $q = "SELECT
            count(1) as c
        FROM
            attendance
        WHERE
            id <> $id
            and employee_id = 1
            and 
            (
                in_time between '$in_date' and '$out_time'
                or out_time between '$in_date' and '$out_time'
                or 
                (
                    '$in_date' < in_time and '$out_time' > out_time
                )
            )";

    $record = $mysql->select($q);

    if ($record && $record[0]['c'] > 0)
    {
        Session::writeFlash("fail", "Attendance conflict with another slot");
    }
    else
    {
        $model = new App\Model\Attendance();
        
        $model->id = $_GET['id'];
        if ($model->update($_POST['form_data']))
        {
            Session::writeFlash("success", "Record has been updated.");
            redirect("attendance/summary");
        }
        else
        {
            Session::writeFlash("fail", "Fail To Update.");
        }
    }
}

require_once './app/resource/layout/main/head.php'
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Attendance Edit</h4>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-lg-6">
                        <form method="post">
                            <div class="mb-3">
                                <b>Location : </b> <?=  $location_record['name'] ?>
                                <br/><br/>
                                <b>Employee : </b> <?=  $employee_record['name'] ?>, <?=  $employee_record['mobile'] ?>
                            </div>

                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <?= $form->label("in_time", ["class" => "form-label", "required" => true]); ?>
                                        <?= $form->input("in_time", ["class" => "form-control date-time-picker",
                                            "id" => "start_date_picker",
                                            "data-date-end" => "#end_date_picker",
                                            "required" => true
                                        ]); ?>
                                    </div>
                                    <div class="col-lg-6">
                                        <?= $form->label("out_time", ["class" => "form-label", "required" => true]); ?>
                                        <?= $form->input("out_time", ["class" => "form-control date-time-picker",
                                            "id" => "end_date_picker",
                                            "data-date-start" => "#start_date_picker",
                                            "required" => true
                                        ]); ?>
                                    </div>
                                </div>
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