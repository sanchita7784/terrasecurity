<?php

require_once './app/model/Employee.php';
require_once './app/model/Location.php';
require_once './app/model/Attendance.php';
require_once './app/include/Form.php';

$model = new App\Model\Employee();

$form = new App\Form($model);

$location = new App\Model\Location();

$location_list = $location->findListCache("id", "name");

$employee = new App\Model\Employee();

$records = $employee->find(["id", "name", "mobile"]);
$employee_list = [];

foreach($records as $record)
{
    $employee_list[$record['id']] = $record['name'] . " (" . $record['mobile'] . ")";
}

if (isset($_POST['form_data']))
{
    $model = new App\Model\Attendance();

    if ($_POST['form_data']['type'] == 'out')
    {
        $last_record = $model->find([], ["employee_id" => $_POST['form_data']['employee_id']], "id", "desc", 0, 1);

        if ($last_record)
        {
            $last_record = $last_record[0];

            if ($last_record['out_time'])
            {
                $_POST['form_data']['out_time'] = date(DateUtility::DATETIME_FORMAT);
                if ($model->insert($_POST['form_data']))
                {
                    Session::writeFlash("success", "Record has been Saved");
                    redirect("attendance/mark");
                }
                else
                {
                    Session::writeFlash("fail", "Fail To Save");
                }
            }
            else
            {
                $model->id = $last_record['id'];
                $_POST['form_data']['out_time'] = date(DateUtility::DATETIME_FORMAT);
                if ($model->update($_POST['form_data']))
                {
                    Session::writeFlash("success", "Record has been updated.");
                    redirect("attendance/mark");
                }
                else
                {
                    Session::writeFlash("fail", "Fail To Update.");
                }
            }
        }
    }
    else
    {
        $_POST['form_data']['in_time'] = date(DateUtility::DATETIME_FORMAT);
        if ($model->insert($_POST['form_data']))
        {
            Session::writeFlash("success", "Record has been Saved");
            redirect("attendance/mark");
        }
        else
        {
            Session::writeFlash("fail", "Fail To Save");
        }
    }
}

require_once './app/resource/layout/main/head.php'
?>

<style>
    .in_out{
        margin: 10px; 
        color : white; 
        background-color: black;        
        border-radius:50%;
        font-size: 30px;        
        width : 100px;
        height : 100px;
        text-align: center;
        padding: 25px;
        cursor: pointer;
    }

    .in_active{
        background-color: green;
    }

    .out_active{
        background-color: red;
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Attendance Form</h4>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-lg-6">
                        <form method="post">
                            <div class="mb-3">
                                <?= $form->label("location", ["class" => "form-label"]); ?>
                                <?= $form->input("location_id", ["class" => "form-control select2",
                                    "type" => "select",
                                    "list" => $location_list,
                                    "value" => $auth->user['location_id'],
                                    "empty" => true,
                                    "required" => true
                                ]); ?>
                            </div>
                            <div class="mb-3">
                                <?= $form->label("employee", ["class" => "form-label"]); ?>
                                <?= $form->input("employee_id", ["class" => "form-control select2",
                                    "id" => "employee_id",
                                    "type" => "select",
                                    "list" => $employee_list,
                                    "empty" => true,
                                    "required" => true
                                ]); ?>

                                <img id="emp_photo" src="" alt="Photo" style="max-width: 100px; max-height: 100px; display: none;" />
                                <span id="last_attendance_info" style="display: none;"></span>                              
                            </div>
                            
                            <div class="d-flex" class="mb-3">
                                <div id="in" class="in_out">IN</div>
                                <div id="out" class="in_out">Out</div>
                                <?= $form->input("type", ["type" => "hidden", "id" => "type"]); ?>
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


<script>
    $(function(){
        $("#employee_id").change(function (){
        
            var v = $(this).val();
            if (v)
            {
                var url = "index.php?r=attendance/emp_details&emp_id=" + v
                $.get(url, function(responseText){
                    ajaxHandleResponse(url, responseText, function (response) 
                    {
                        var data = response['data'];
                        if (typeof data['employee']['image'] != "undefined")
                        {
                            $("#emp_photo").attr("src", data['employee']['image']);
                            $("#emp_photo").show();
                        }
                    });                        
                });
            }

        }).trigger("change", {pageLoad : true});

        $("#in").click(function(){
            $("#in").addClass("in_active");
            $("#out").removeClass("out_active");
            $("#type").val("in");
        });

        $("#out").click(function(){
            $("#in").removeClass("in_active");
            $("#out").addClass("out_active");
            $("#type").val("out");
        });

        $("form").submit(function(){
            if (!$("#type").val())
            {
                alert("Please Select In or Out");
                return false;
            }
        })
    });
    
</script>
<?php require_once './app/resource/layout/main/foot.php' ?>