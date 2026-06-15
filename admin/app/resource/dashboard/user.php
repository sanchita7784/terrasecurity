<?php

use App\Auth;
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


$transaction = new Transaction();
$auth = new Auth();

$total_transaction = $transaction->findCount(Condition::init("AND")->add("user_id", $auth->user['id']));
$pending_transaction = $transaction->findCount(Condition::init("AND")->add("status", "pending")->add("user_id", $auth->user['id']));
$complete_transaction = $transaction->findCount(Condition::init("AND")->add("status", "COMPLETED")->add("user_id", $auth->user['id']));
$failed_transaction = $transaction->findCount(Condition::init("AND")->add("status", "FAILED")->add("user_id", $auth->user['id']));


require_once './app/resource/layout/main/head.php'
?>
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Dashboard</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <!-- card -->
            <div class="card card-h-100">
                <!-- card body -->
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Transaction</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="<?= $total_transaction ?>"><?=  formatToMillion($total_transaction) ?> </span>
                            </h4>
                        </div>
                    </div>                    
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->
        <div class="col-xl-3 col-md-6">
            <!-- card -->
            <div class="card card-h-100">
                <!-- card body -->
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Pending Transaction</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="<?= $pending_transaction ?>"><?=  formatToMillion($pending_transaction) ?> </span>
                            </h4>
                        </div>
                    </div>                    
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->

        <div class="col-xl-3 col-md-6">
            <!-- card -->
            <div class="card card-h-100">
                <!-- card body -->
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Complete Transaction</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="<?= $complete_transaction ?>"><?=  formatToMillion($complete_transaction) ?> </span>
                            </h4>
                        </div>
                    </div>                    
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->

        <div class="col-xl-3 col-md-6">
            <!-- card -->
            <div class="card card-h-100">
                <!-- card body -->
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Failed Transaction</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="<?= $failed_transaction ?>"><?=  formatToMillion($failed_transaction) ?> </span>
                            </h4>
                        </div>
                    </div>                    
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->

    </div><!-- end row-->
</div>
                    

<?php require_once './app/resource/layout/main/foot.php' ?>