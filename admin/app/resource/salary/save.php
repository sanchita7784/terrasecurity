<?php

use App\Form;
use App\Model\Salary;

require_once './app/model/Salary.php';

$model = new Salary();

$form = new Form($model);

// d($form->data); exit;

if (isset($_GET['form_data']['month_year']) && $_GET['form_data']['month_year'])
{
    $from_date = DateUtility::getDate("01-" . $_GET['form_data']['month_year'], DateUtility::DATE_FORMAT);
    $to_date = DateUtility::getDate("01-" . $_GET['form_data']['month_year'], "Y-m-t");

    $attendance_records = $model->get_attendance($from_date, $to_date);

    $salary_data = $model->get_salary_from_attendance($_GET['form_data']['month_year'], $attendance_records);

    $month = DateUtility::getDate("01-" . $_GET['form_data']['month_year'], "m");
    $year = DateUtility::getDate("01-" . $_GET['form_data']['month_year'], "Y");
}

if (isset($_POST['form_data']))
{
    // d($_POST['form_data']); exit;

    $insert_count = $update_count = $fail_count = 0;

    foreach($_POST['form_data'] as $emp_id => $shift_data)
    {
        foreach($shift_data as $shift_hours => $record)
        {
            $db_record = $model->find(["id"], [
                "employee_id" => $emp_id,
                "month" => $record['month'],
                "year" => $record['year'],
                "shift_hours" => $shift_hours
            ]);

            if ($db_record)
            {
                $model->id = $db_record[0]['id'];
                if ($model->update($record))
                {
                    $update_count++;
                }
                else
                {
                    $fail_count++;
                }
            }
            else
            {
                if ($model->insert($record))
                {
                    $insert_count++;
                    
                }
                else
                {
                    $fail_count++;
                }
            }
        }
    }

    if ($insert_count || $update_count)
    {
        Session::writeFlash("success", "Records Insert : $insert_count, Records Update : $update_count");
    }

    if ($fail_count > 0)
    {
        Session::writeFlash("fail", "Fail To save : $fail_count");
    }
}

require_once './app/resource/layout/main/head.php'
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Salary Form</h4>
            </div>
            <div class="card-body p-4">
                <form style="width : 80%">
                    <input type="hidden" name="r" value="<?= $resource ?>" />
                    <div class="row">
                        <div class="col-md-3 col-xl-2">
                            <?= $form->input("month_year", [
                                "class" => "form-control month-date-picker",
                                "required" => true
                            ]); ?>
                        </div>
                        <div class="col-md-3 col-xl-2">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="<?= url_without_query_params($resource) ?>" class="btn btn-secondary">Clear</a>
                        </div>
                    </div>
                </form>
            </div>

            <?php if (isset($salary_data)): ?>
            <form method="post">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <input type="checkbox" id="parent-chk" data-sr-chkselect-children=".child-chk">
                                    #
                                </th>
                                <th>Emp. Details</th>
                                <th>Cal. Salary</th>
                                <th>Problems</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 0; foreach($salary_data as $emp_id => $shift_records):?>
                                <?php
                                foreach($shift_records as $shift_hours => $record):
                                    $i++;
                                ?>
                                <tr>
                                    <td>
                                        <?php if (isset($record['is_paid']) && $record['is_paid'] == 0): ?>
                                            <label>
                                                <input type="checkbox" name="form_data[<?= $emp_id ?>][<?= $shift_hours ?>][is_check]" class="child-chk">
                                                <input type="hidden" name="form_data[<?= $emp_id ?>][<?= $shift_hours ?>][employee_id]" value="<?=  $record['employee']['id'] ?>">
                                                <input type="hidden" name="form_data[<?= $emp_id ?>][<?= $shift_hours ?>][month]" value="<?= $month ?>">
                                                <input type="hidden" name="form_data[<?= $emp_id ?>][<?= $shift_hours ?>][year]" value="<?= $year ?>">
                                                <input type="hidden" name="form_data[<?= $emp_id ?>][<?= $shift_hours ?>][shift_hours]" value="<?=  $record['shift_hours'] ?>">
                                                <input type="hidden" name="form_data[<?= $emp_id ?>][<?= $shift_hours ?>][attendance_hours]" value="<?=  $record['attendance_hours'] ?>">
                                                <input type="hidden" name="form_data[<?= $emp_id ?>][<?= $shift_hours ?>][holidays]" value="<?=  $record['holidays'] ?>">
                                                <input type="hidden" name="form_data[<?= $emp_id ?>][<?= $shift_hours ?>][leaves]" value="<?=  $record['leaves'] ?>">
                                                <input type="hidden" name="form_data[<?= $emp_id ?>][<?= $shift_hours ?>][present_days]" value="<?=  $record['present_days'] ?>">
                                                <input type="hidden" name="form_data[<?= $emp_id ?>][<?= $shift_hours ?>][declare_salary]" value="<?= $record['employee']['salary'] ?>">
                                                <input type="hidden" name="form_data[<?= $emp_id ?>][<?= $shift_hours ?>][one_hour_salary]" value="<?= $record['one_hour_salary'] ?>">
                                                <input type="hidden" name="form_data[<?= $emp_id ?>][<?= $shift_hours ?>][one_day_salary]" value="<?= $record['one_day_salary'] ?>">
                                                <input type="hidden" name="form_data[<?= $emp_id ?>][<?= $shift_hours ?>][cal_salary]" value="<?= $record['cal_salary'] ?>">
                                                <?= $i ?>
                                            </label>
                                        <?php else: ?>
                                            <?= $i ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <b>Name : </b> <?=  $record['employee']['name'] ?>
                                        <br/>
                                        <b>M. : </b> <?=  $record['employee']['mobile'] ?>
                                        <br/>
                                        <b>Salary : </b> <?=  $record['employee']['salary'] ?>
                                        <br/>
                                    </td>
                                    <td>
                                        <?php if (isset($record['cal_salary'])): ?>
                                            <b>Shift Hours : </b> <?=  $record['shift_hours'] ?>
                                            <br/>
                                            <b>Attendance Hours : </b> <?=  $record['attendance_hours'] ?>
                                            <br/>
                                            <b>Holidays : </b> <?=  $record['holidays'] ?>
                                            <br/>
                                            <b>Leaves : </b> <?=  $record['leaves'] ?>
                                            <br/>
                                            <b>Declare Salary : </b> <?= $record['employee']['salary'] ?>
                                            <br/>
                                            <b>One Day Salary : </b> <?=  $record['one_day_salary'] ?>
                                            <br/>
                                            <b>One Hour Salary : </b> <?=  $record['one_hour_salary'] ?>
                                            <br/>
                                            <b>Salary : </b> <?=  $record['cal_salary'] ?>
                                            <br/>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (isset($record['problems'])):
                                            foreach($record['problems'] as $problem):
                                        ?>
                                            <span class="text-danger"><?=  $problem ?></span>

                                        <?php endforeach; endif; ?>

                                        <?php if (isset($record['is_paid']) && $record['is_paid']): ?>
                                            <span class="text-success">Paid</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            </form>
            <?php endif; ?>
        </div>
    </div> <!-- end col -->
</div>

<script>
    $(function(){
        $(".month-date-picker").datepicker({
            format: "M-yyyy",      // Sets the output format in the text field
            startView: "months",    // The view that the picker starts on when opened
            minViewMode: "months",  // Limits the lowest view depth to month level (prevents day selection)
            autoclose: true
        });

        $("#parent-chk").chkSelectAll();
    });
</script>

<?php require_once './app/resource/layout/main/foot.php' ?>