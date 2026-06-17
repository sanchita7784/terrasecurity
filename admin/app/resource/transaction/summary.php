<?php

use App\Auth;
use App\Form;
use App\Model\PaymentGateway;
use App\Model\User;
use App\Model\Transaction;
use HardeepVicky\QueryBuilder\Condition;
use HardeepVicky\QueryBuilder\QuerySelect;
use HardeepVicky\QueryBuilder\Table;

require_once './app/model/BaseModel.php';
require_once './app/model/User.php';
require_once './app/model/Transaction.php';
require_once './app/model/PaymentGateway.php';
require_once './app/include/Form.php';

$auth = new Auth();

$model = new Transaction();

$condition = Condition::init("AND");

if (isset($_GET['form_data']['user_id']))
{
    $condition->add("user_id", $_GET['form_data']['user_id']);
}

if (isset($_GET['form_data']['payment_gateway_id']))
{
    $condition->add("payment_gateway_id", $_GET['form_data']['payment_gateway_id']);
}

if (isset($_GET['form_data']['status']))
{
    $condition->add("status", $_GET['form_data']['status']);
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

$model->user_id($records);
$model->payment_gateway_id($records);

$user = new User();
$user_list = $user->findListCache("id", "name");

$payment_gateway = new PaymentGateway;
$payment_gateway_list = $payment_gateway->findListCache("id", "name");

$form = new Form($model);

require_once './app/resource/layout/main/head.php'
?>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Transaction Summary</h4>
        <p class="card-title-desc">
            <div class="d-flex justify-content-between">
                <form style="width : 80%">
                    <input type="hidden" name="r" value="transaction/summary" />
                    <div class="row">                        
                        <div class="col-md-3 col-xl-2">
                            <?= $form->input("user_id", [
                                "class" => "form-control js-choice", 
                                "type" => "select", 
                                "list" => $user_list,
                                "empty" => "Select User",
                                ]); 
                            ?>
                        </div>
                        <div class="col-md-4 col-xl-3">
                            <?= $form->input("payment_gateway_id", [
                                "class" => "form-control js-choice", 
                                "type" => "select", 
                                "list" => $payment_gateway_list,
                                "empty" => "Select Payment Gateway"
                                ]); 
                            ?>
                        </div>
                        <div class="col-md-3 col-xl-2">
                            <?= $form->input("status", [
                                "class" => "form-control js-choice", 
                                "type" => "select", 
                                "list" => Transaction::STATUS_LIST,
                                "empty" => "Select Status"
                                ]); 
                            ?>
                        </div>
                        <div class="col-md-3 col-xl-2">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="<?= url_without_query_params("transaction/summary") ?>" class="btn btn-secondary">Clear</a>
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
                        <th>User</th>
                        <th>Payment Gateway</th>
                        <th>
                            <?= sortable_link("amount", "Amount") ?>
                        </th>
                        <th>Status</th>
                        <th>Datetime</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($records as $record):?>
                    <tr>
                        <th scope="row"><?= $record['id']  ?></th>
                        <td><?= $record['user_id']['name'] ?? ""  ?></td>
                        <td><?= $record['payment_gateway_id']['name'] ?? ""  ?></td>
                        <td><?= $record['amount'] ?></td>
                        <td><?= isset(Transaction::STATUS_LIST[$record['status']]) ? Transaction::STATUS_LIST[$record['status']] : $record['status'] ?></td>
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