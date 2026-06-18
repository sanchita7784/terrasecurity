<?php

use App\Form;
use App\Model\Holiday;
use App\Model\HolidayDetail;

require_once './app/model/Holiday.php';
require_once './app/model/HolidayDetail.php';

$model = new Holiday();

$holidayDetail = new HolidayDetail();

$form = new Form($model);

$holidayDetail = new HolidayDetail();

if (isset($_GET['id']))
{
    $date_list = $holidayDetail->findList("id", "date", ["holiday_id" => $_GET['id']]);

    foreach($date_list as $k => $date)
    {
        $date_list[$k] = DateUtility::getDate($date, DateUtility::DATE_OUT_FORMAT);
    }

    $form->data['date'] = implode(",", $date_list);
}

// d($form->data); exit;

if (isset($_POST['form_data']))
{
    $holiday_data = [
        "name" => $_POST['form_data']['name']
    ];

    $saved = false;

    $mysql->query("START TRANSACTION;");

    if (isset($_GET['id']))
    {
        $model->id = $_GET['id'];
        if ($model->update($holiday_data))
        {            
            $saved = true;
        }
        else
        {
            Session::writeFlash("fail", "Fail To Update.");
        }
    }
    else
    {
        if ($model->insert($holiday_data))
        {
            $saved = true;
        }
        else
        {
            Session::writeFlash("fail", "Fail To Save");
        }
    }

    if ($saved)
    {
        $records = $holidayDetail->find([], ["holiday_id" => $model->id]);

        foreach($records as $record)
        {
            $holidayDetail->delete($record['id']);
        }

        $dates = explode(",", $_POST['form_data']['date']);

        foreach($dates as $date)
        {
            $result = $holidayDetail->insert([
                "holiday_id" => $model->id,
                "date" => DateUtility::getDate($date, DateUtility::DATE_FORMAT)
            ]);

            if (!$result)
            {
                $saved = false;
            }
        }

        if ($saved)
        {
            $mysql->query("COMMIT;");
            Session::writeFlash("success", "Record has been saved.");
            redirect("holiday/summary");
        }
        else
        {
            $mysql->query("ROLLBACK;");
            Session::writeFlash("fail", "Fail To Save");
        }
    }
}

require_once './app/resource/layout/main/head.php' 
?>
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Holiday Form</h4>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-lg-6">
                        <form method="post">                           
                            <div class="mb-3">
                                <?= $form->label("name", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("name", ["class" => "form-control", 
                                    "required" => true
                                ]); ?>
                            </div>  
                            <div class="mb-3">
                                <?= $form->label("date", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("date", [
                                    "class" => "form-control multi-date-picker",
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

<script>
    $(function(){
        $(".multi-date-picker").datepicker({
            multidate: true,
            format: 'dd-M-yyyy',
        });
    });
</script>

<?php require_once './app/resource/layout/main/foot.php' ?>