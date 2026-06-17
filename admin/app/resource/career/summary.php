<?php

use App\Model\Career;
use HardeepVicky\QueryBuilder\Condition;
use HardeepVicky\QueryBuilder\QuerySelect;
use HardeepVicky\QueryBuilder\Table;

require_once './app/model/BaseModel.php';
require_once './app/model/Career.php';

$model = new App\Model\Career();

$condition = Condition::init("AND");

if (isset($_GET['name']) && $_GET['name'])
{
    $condition->add("name", '%' . $_GET['name'] .'%', "like");
}

if (isset($_GET['phone']) && $_GET['phone'])
{
    $condition->add("phone", '%' . $_GET['phone'] .'%', "like");
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

require_once './app/resource/layout/main/head.php'
?>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Career Summary</h4>
        <p class="card-title-desc">
            <div class="d-flex justify-content-between">
                <form style="width : 80%">
                    <input type="hidden" name="r" value="<?= $resource ?>" />
                    <div class="row">
                        <div class="col-md-3 col-xl-2">
                            <input class="form-control" placeholder="Name" name="name" value="<?=  $_GET['name'] ?? "" ?>"/>
                        </div>
                        <div class="col-md-3 col-xl-2">
                            <input class="form-control" placeholder="Phone" name="phone" value="<?=  $_GET['phone'] ?? "" ?>"/>
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
                        <th>
                            <?= sortable_link("email", "Email") ?>
                        </th>
                        <th>
                            <?= sortable_link("phone", "Phone") ?>
                        </th>
                        <th>
                            <?= sortable_link("role", "Role") ?>
                        </th>
                        <th>
                            CV
                        </th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($records as $record):?>
                    <tr>
                        <th scope="row"><?= $record['id']  ?></th>
                        <td><?= $record['name']  ?></td>
                        <td><?= $record['email']  ?></td>
                        <td><?= $record['phone']  ?></td>
                        <td><?= $record['role']  ?></td>
                        <td>
                            <a href="/<?= $record['cv'] ?>" download>CV Download</a>
                        </td>
                        <td>
                            <?=  DateUtility::getDate($record['created_at'], DateUtility::DATETIME_OUT_FORMAT) ?>
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