<?php

use App\Model\User;
use HardeepVicky\QueryBuilder\Condition;
use HardeepVicky\QueryBuilder\QuerySelect;
use HardeepVicky\QueryBuilder\Table;

require_once './app/model/Employee.php';

$model = new App\Model\Employee();

$condition = Condition::init("AND");

if (isset($_GET['name']) && $_GET['name'])
{
    $condition->add("name", '%' . $_GET['name'] .'%', "like");
}


if (isset($_GET['mobile']) && $_GET['mobile'])
{
    $condition->add("mobile", '%' . $_GET['mobile'] .'%', "like");
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
$model->updated_by($records);


require_once './app/resource/layout/main/head.php'
?>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Employee</h4>
        <p class="card-title-desc">
            <div class="d-flex justify-content-between">
                <form style="width : 80%">
                    <input type="hidden" name="r" value="<?= $resource ?>" />
                    <div class="row">
                        <div class="col-md-3 col-xl-2">
                            <input class="form-control" placeholder="Name" name="name" value="<?=  $_GET['name'] ?? "" ?>"/>
                        </div>
                        <div class="col-md-3 col-xl-2">
                            <input class="form-control" placeholder="Mobile" name="mobile" value="<?=  $_GET['mobile'] ?? "" ?>"/>
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
        Showing Page <?= $page ?> of <?= $total_pages ?>, Start : <?= $start + 1 ?>, End : <?=  $end ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>
                            <?= sortable_link("name", "Name") ?>
                        </th>
                        <th>Photo</th>
                        <th>Mobile</th>
                        <th>Salary</th>
                        <th>Address</th>
                        <th>Created</th>
                        <th>Created By</th>
                        <th>Updated By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($records as $record):?>
                    <tr>
                        <th scope="row"><?= $record['id']  ?></th>
                        <td><?= $record['name']  ?></td>
                        <td>
                            <?php if (!empty($record['image'])): ?>
                                <img src="<?= $record['image'] ?>" alt="Photo" style="max-width: 100px; max-height: 100px;">
                            <?php endif; ?>
                        </td>
                        <td><?= $record['mobile']  ?></td>
                        <td><?= $record['salary']  ?></td>
                        <td><?= $record['address']  ?></td>
                        <td>
                            <?=  DateUtility::getDate($record['created_at'], DateUtility::DATETIME_OUT_FORMAT) ?>
                        </td>
                        <td>
                            <?= $record['created_by']['name'] . "-" . $record['created_by']['id'] ?? "" ?>
                        </td>
                        <td>
                            <?php if (isset($record['updated_by'])): ?>
                                <?= $record['updated_by']['name'] . "-" . $record['updated_by']['id'] ?? "" ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a class="btn btn-sm btn-secondary" href="<?= url("employee/save", ["id" => $record['id']]) ?>">
                                <i class="fas fa-edit"></i>
                            </a>

                            <a class="btn btn-sm btn-danger confirm" data-msg="Are you sure to delete?" href="<?= url("employee/delete", ["id" => $record['id']]) ?>">
                                <i class="fas fa-trash"></i>
                            </a>

                            <a href="<?= url("employee/shift", ["employee_id" => $record['id']]) ?>">
                               Shift
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