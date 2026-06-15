<?php

use App\Model\Role;
use HardeepVicky\QueryBuilder\Condition;
use HardeepVicky\QueryBuilder\QuerySelect;
use HardeepVicky\QueryBuilder\Table;

require_once './app/model/BaseModel.php';
require_once './app/model/User.php';
require_once './app/model/Role.php';

$user = new App\Model\User();

$condition = Condition::init("OR");

if (isset($_GET['name']))
{
    $condition->add("name", '%' . $_GET['name'] .'%', "like");
    $condition->add("email", '%' . $_GET['name'] . '%', "like");
    $condition->add("mobile", '%' . $_GET['name'] . '%', "like");
}

$total_count = $user->findCount($condition);
$limit = 20;
$page = $_GET['page'] ?? 1;
$order_by = $_GET['order_by'] ?? 'id';
$order_dir = $_GET['order_dir'] ?? 'desc';

$total_pages = ceil($total_count / $limit);
$end = $page * $limit;
$start = $end - $limit;

$qs = new QuerySelect(new Table($user->getTable()));
$qs->setWhere($condition);
$qs->order($order_by, $order_dir);
$qs->setOffset($start);
$qs->setLimit($limit);

$records = $user->findQuery($qs);

$role = new Role();
$role_list = $role->findListCache("id", "name");

require_once './app/resource/layout/main/head.php'
?>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">User Summary</h4>
        <p class="card-title-desc">
            <div class="d-flex justify-content-between">
                <form style="width : 80%">
                    <input type="hidden" name="r" value="user/summary" />
                    <div class="row">
                        <div class="col-md-3 col-xl-2">
                            <input class="form-control" placeholder="Search" name="name" value="<?=  $_GET['name'] ?? "" ?>"/>
                        </div>
                        <div class="col-md-3 col-xl-2">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="<?= url_without_query_params("user/summary") ?>" class="btn btn-secondary">Clear</a>
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
                            <?= sortable_link("username", "Username") ?>
                        </th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Active / De-active</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($records as $record):?>
                    <tr>
                        <th scope="row"><?= $record['id']  ?></th>
                        <td><?= $record['name']  ?></td>
                        <td><?= $record['username']  ?></td>
                        <td><?= $role_list[$record['role_id']] ?? ""  ?></td>
                        <td><?= $record['email']  ?></td>
                        <td><?= $record['mobile']  ?></td>
                        <td><?= $record['is_active'] ? "<span class='badge bg-success'>Yes</span>" : "<span class='badge bg-danger'>No</span>"  ?></td>
                        <td>
                            <?= DateUtility::getDate($record['created_at'], DateUtility::DATETIME_OUT_FORMAT); ?>
                        </td>
                        <td>
                            <a class="btn btn-sm btn-secondary" href="<?= url("user/save", ["id" => $record['id']]) ?>">
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