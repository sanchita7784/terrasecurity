<?php

use App\Model\User;
use csv\CsvUtility;
use HardeepVicky\QueryBuilder\Condition;
use HardeepVicky\QueryBuilder\QuerySelect;
use HardeepVicky\QueryBuilder\Table;

require_once './app/model/Attendance.php';
require_once './app/model/Location.php';
require_once './app/model/Employee.php';

$location = new App\Model\Location();

$location_list = $location->findListCache("id", "name");

$employee = new App\Model\Employee();

$records = $employee->find(["id", "name", "mobile"]);
$employee_list = [];

foreach($records as $record)
{
    $employee_list[$record['id']] = $record['name'] . " (" . $record['mobile'] . ")";
}

$model = new App\Model\Attendance();

$condition = Condition::init("AND");

if (isset($_GET['form_data']['employee_id']) && $_GET['form_data']['employee_id'])
{
    $condition->add("employee_id", $_GET['form_data']['employee_id']);
}

if (isset($_GET['form_data']['location_id']) && $_GET['form_data']['location_id'])
{
    $condition->add("location_id", $_GET['form_data']['location_id']);
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

if (isset($_GET['export']) && $_GET['export'] == "csv")
{
    $records = $model->findQuery($qs);

    $model->dateFields($records);    
    $csv_records = [];
    foreach($records as $record)
    {
        $csv_records[] = [
            'ID' => $record['id'],
            'Location' => $location_list[$record['location_id']] ?? "",
            "Employee" =>  $employee_list[$record['employee_id']] ?? "",
            "In Time" => $record['in_time'] ?? "",
            "Out Time" => $record['out_time'] ?? "",
        ];
    }

    $path = "storage/files/temp/";
    FileUtility::createFolder($path);
    $file = $path . "attendance_" . date(DateUtility::DATETIME_OUT_FORMAT) . ".csv";
    
    $csvUtility = new CsvUtility($file);
    $csvUtility->write($csv_records);

    download_start($file, "application/octet-stream");
}

$qs->setOffset($start);
$qs->setLimit($limit);

$records = $model->findQuery($qs);

$model->dateFields($records);

$form = new App\Form($model);

require_once './app/resource/layout/main/head.php'
?>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Attendance Summary</h4>
        <p class="card-title-desc">
            <div class="d-flex justify-content-between">
                <form style="width : 80%">
                    <input type="hidden" name="r" value="<?= $resource ?>" />
                    <div class="row">
                        <div class="col-md-4 col-xl-3">
                            <?= $form->input("location_id", ["class" => "form-control select2",
                                "type" => "select",
                                "list" => $location_list,
                                "empty" => true,
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
        <a class="btn btn-secondary" href="<?= url($_GET['r'], ["export" => "csv"])  ?>">Export CSV</a>
        Showing Page <?= $page ?> of <?= $total_pages ?>, Start : <?= $start + 1 ?>, End : <?=  $end ?>
        <br/><br/>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Location</th>
                        <th>Employee</th>
                        <th>In</th>
                        <th>Out</th>                        
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($records as $record):?>
                    <tr>
                        <th scope="row"><?= $record['id']  ?></th>
                        <td><?= $location_list[$record['location_id']] ?? ""  ?></td>
                        <td><?= $employee_list[$record['employee_id']] ?? ""  ?></td>
                        <td><?= $record['in_time']  ?></td>
                        <td><?= $record['out_time']  ?></td>
                        <td>
                            <a class="btn btn-sm btn-secondary" href="<?= url("attendance/edit", ["id" => $record['id']]) ?>">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>                    
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?= pagination_links($total_pages, $page); ?> 
    </div>
    <!-- end card body -->
</div>

<?php require_once './app/resource/layout/main/foot.php' ?>