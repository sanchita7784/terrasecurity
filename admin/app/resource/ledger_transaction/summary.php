<?php

use HardeepVicky\QueryBuilder\Condition;
use HardeepVicky\QueryBuilder\QuerySelect;
use HardeepVicky\QueryBuilder\Table;

require_once './app/model/LedgerTransaction.php';
require_once './app/model/LedgerAccount.php';

$model = new App\Model\LedgerTransaction();

$condition = Condition::init("AND");

if (isset($_GET['form_data']['company_id']) && $_GET['form_data']['company_id'])
{
    $condition->add("company_id", $_GET['form_data']['company_id']);
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

$LedgerAccount = new App\Model\LedgerAccount();

$la_list = $LedgerAccount->findList();

$form = new App\Form($model);

require_once './app/resource/layout/main/head.php'
?>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Ledger Account</h4>
        <p class="card-title-desc">
            <div class="d-flex justify-content-between">
                <form style="width : 80%">
                    <input type="hidden" name="r" value="<?= $resource ?>" />
                    <div class="row">
                        <div class="col-md-4 col-xl-3">
                            <?= $form->input("legder_account_id", ["class" => "form-control select2",
                                "type" => "select",
                                "list" => $la_list,
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
        Showing Page <?= $page ?> of <?= $total_pages ?>, Start : <?= $start + 1 ?>, End : <?=  $end ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>                        
                        <th>Ledger Account</th>                        
                        <th>Amount</th>
                        <th>Comments</th>
                        <th>Created</th>
                        <th>Created By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($records as $record):?>
                    <tr>
                        <th scope="row"><?= $record['id']  ?></th>
                        <td><?= $la_list[$record['legder_account_id']] ?? ""  ?></td>
                        <td><?= $record['amount']  ?></td>
                        <td><?= $record['comments']  ?></td>
                        <td>
                            <?=  DateUtility::getDate($record['created_at'], DateUtility::DATETIME_OUT_FORMAT) ?>
                        </td>
                        <td>
                            <?= $record['created_by']['name'] . "-" . $record['created_by']['id'] ?? "" ?>
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