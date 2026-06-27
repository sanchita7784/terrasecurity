<?php

use App\Form;

require_once './app/model/LedgerTransaction.php';
require_once './app/model/LedgerAccount.php';

$model = new App\Model\LedgerTransaction();

$form = new Form($model);

if (isset($_POST['form_data']))
{
    if ($model->insert($_POST['form_data']))
    {
        Session::writeFlash("success", "Record has been Saved");
        redirect("ledger_transaction/summary");
    }
    else
    {
        Session::writeFlash("fail", "Fail To Save");
    }
}

$LedgerAccount = new App\Model\LedgerAccount();

$la_list = $LedgerAccount->findList();

$form = new App\Form($model);


require_once './app/resource/layout/main/head.php' 
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Ledger Account Form</h4>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-lg-6">
                        <form method="post">                           
                            <div class="mb-3">
                                <?= $form->label("legder_account", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("legder_account_id", ["class" => "form-control select2", 
                                    "type" => "select",
                                    "list" => $la_list,
                                    "empty" => true,
                                    "required" => true
                                ]); ?>
                            </div>
                            <div class="mb-3">
                                <?= $form->label("amount", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("amount", ["class" => "form-control validate-float", "required" => true]); ?>
                            </div>                                                   
                            <div class="mb-3">
                                <?= $form->label("comments", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("comments", ["type" => "textarea", "class" => "form-control", "required" => true]); ?>
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

<?php require_once './app/resource/layout/main/foot.php' ?>