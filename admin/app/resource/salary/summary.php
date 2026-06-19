<?php

use HardeepVicky\QueryBuilder\Condition;
use HardeepVicky\QueryBuilder\QuerySelect;
use HardeepVicky\QueryBuilder\Table;

require_once './app/model/Salary.php';
require_once './app/model/Employee.php';

$model = new App\Model\Salary();

if (isset($_POST['action']) && $_POST['action'] == "pay_now")
{
    $count = 0;
    foreach($_POST['form_data']['ids'] as $id => $on)
    {
        if ($on)
        {
            $model->id = $id;
            if ($model->update(["is_paid" => 1]))
            {
                $count++;
            }
        }
    }

    Session::writeFlash("success", "$count Salary has been Paid.");
}
else if (isset($_POST['action']) && $_POST['action'] == "unpay_now")
{
    $count = 0;

    foreach($_POST['form_data']['ids'] as $id => $on)
    {
        if ($on)
        {
            $model->id = $id;
            if ($model->update(["is_paid" => 0]))
            {
                $count++;
            }
        }
    }

    Session::writeFlash("success", "$count Salary has been Un-Paid.");
}

$employee = new App\Model\Employee();

$records = $employee->find(["id", "name", "mobile"]);
$employee_list = [];

foreach($records as $record)
{
    $employee_list[$record['id']] = $record['name'] . " (" . $record['mobile'] . ")";
}

$condition = Condition::init("AND");

if (isset($_GET['form_data']['month_year']) && $_GET['form_data']['month_year'])
{
    $month = DateUtility::getDate("01-" . $_GET['form_data']['month_year'], "m");
    $year = DateUtility::getDate("01-" . $_GET['form_data']['month_year'], "Y");

    $condition->add("month", $month);
    $condition->add("year", $year);
}

if (isset($_GET['form_data']['employee_id']) && $_GET['form_data']['employee_id'])
{
    $condition->add("employee_id", $_GET['form_data']['employee_id']);
}

$total_count = $model->findCount($condition);
$limit = 20;
$page = $_GET['page'] ?? 1;
$order_by = $_GET['order_by'] ?? 'id';
$order_dir = $_GET['order_dir'] ?? 'desc';

$total_pages = ceil($total_count / $limit);
$end = $page * $limit;
$start = $end - $limit;

$qs = new QuerySelect(new Table($model->getTable()));
$qs->setWhere($condition);
$qs->order($order_by, $order_dir);
$qs->setOffset($start);
$qs->setLimit($limit);

$records = $model->findQuery($qs);

$model->created_by($records);

$form = new App\Form($model);

require_once './app/resource/layout/main/head.php'
?>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Salary Summary</h4>
        <p class="card-title-desc">
            <div class="d-flex justify-content-between">
                <form style="width : 80%">
                    <input type="hidden" name="r" value="<?= $resource ?>" />
                    <div class="row">
                        <div class="col-md-3 col-xl-2">
                            <?= $form->input("month_year", [
                                "class" => "form-control month-date-picker",
                            ]); ?>
                        </div>
                        <div class="col-md-4 col-xl-3">
                            <?= $form->input("employee_id", ["class" => "form-control select2",
                                "type" => "select",
                                "list" => $employee_list,
                                "empty" => true,
                            ]); ?>
                        </div>
                        <div class="col-md-3 col-xl-2">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="<?= url_without_query_params($resource) ?>" class="btn btn-secondary">Clear</a>
                        </div>
                    </div>
                </form>
                <div>
                    <?= pagination_links($total_pages, $page); ?>
                </div>
            </div>
        </p>
    </div>
    <div class="card-body">
        <form method="post">
            <button type="submit" class="btn btn-secondary" name="action" value="pay_now">Pay Now</button>
            <button type="submit" class="btn btn-secondary" name="action" value="unpay_now">UnPay Now</button>
            Showing Page <?= $page ?> of <?= $total_pages ?>, Start : <?= $start + 1 ?>, End : <?=  $end ?>
            <br/><br/>
        
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>
                            <input type="checkbox" id="parent-chk" data-sr-chkselect-children=".child-chk">
                            #
                        </th>
                        <th>Employee</th>
                        <th>Month-Year</th>
                        <th>Shift Hours</th>
                        <th>Attendance Hours</th>
                        <th>Holidays</th>
                        <th>Leaves</th>
                        <th>Present Days</th>
                        <th>Declare Salary</th>
                        <th>One Hour Salary</th>
                        <th>One Day Salary</th>
                        <th>Salary</th>
                        <th>Paid</th>
                        <th>Created</th>
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($records as $record):
                        $date = "01-". $record['month'] . '-' . $record['year'];
                        $month_year = DateUtility::getDate($date, "M-Y");
                        ?>
                    <tr>
                        <th scope="row">
                            <label>
                                <input type="checkbox" name="form_data[ids][<?= $record['id'] ?>]" class="child-chk">
                            </label>
                            <?= $record['id']  ?>
                        </th>
                        <td><?= $employee_list[$record['employee_id']]  ?></td>
                        <td><?= $month_year  ?></td>
                        <td><?= $record['shift_hours'] ?></td>
                        <td><?= $record['attendance_hours'] ?></td>
                        <td><?= $record['holidays'] ?></td>
                        <td><?= $record['leaves'] ?></td>
                        <td><?= $record['present_days'] ?></td>
                        <td><?= $record['declare_salary'] ?></td>
                        <td><?= $record['one_hour_salary'] ?></td>
                        <td><?= $record['one_day_salary'] ?></td>
                        <td><?= $record['cal_salary'] ?></td>
                        <td>
                            <?php if($record['is_paid']): ?>
                                <span class="badge bg-success">Paid</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?=  DateUtility::getDate($record['created_at'], DateUtility::DATETIME_OUT_FORMAT) ?>
                        </td>
                        <td>
                            <?= $record['created_by']['name'] . "-" . $record['created_by']['id'] ?? "" ?>
                        </td>
                        <td>
                            <a class="btn btn-sm btn-danger confirm" data-msg="Are you sure to delete?" href="<?= url("salary/delete", ["id" => $record['id']]) ?>">
                                <i class="fas fa-trash"></i>

                        </td>
                    </tr>                    
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        </form>

        <?= pagination_links($total_pages, $page); ?>
    </div>
    <!-- end card body -->
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