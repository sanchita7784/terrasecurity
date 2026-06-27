<?php

use App\Form;
use App\Model\LedgerAccount;

require_once './app/model/Company.php';
require_once './app/model/LedgerAccount.php';

$model = new App\Model\LedgerAccount();

$form = new Form($model);

if (isset($_POST['form_data']))
{
    if (isset($_GET['id']))
    {
        $model->id = $_GET['id'];
        if ($model->update($_POST['form_data']))
        {
            Session::writeFlash("success", "Record has been updated.");
            redirect("ledger_account/summary");
        }
        else
        {
            Session::writeFlash("fail", "Fail To Update.");
        }
    }
    else
    {
        if ($model->insert($_POST['form_data']))
        {
            $ledgerAccount = new LedgerAccount();
            $ledgerAccount->insert([
                "company_id" => $model->id,
                "opening_balance" => $_POST['form_data']['opening_balance']
            ]);

            Session::writeFlash("success", "Record has been Saved");
            redirect("ledger_account/summary");
        }
        else
        {
            Session::writeFlash("fail", "Fail To Save");
        }
    }
}

$company = new App\Model\Company();

$company_list = $company->findListCache("id", "name");



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
                                <?= $form->label("company", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("company_id", ["class" => "form-control select2", 
                                    "type" => "select",
                                    "list" => $company_list,
                                    "empty" => true,
                                    "required" => true
                                ]); ?>
                            </div>               
                            <div class="mb-3">
                                <?= $form->label("opening_balance", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("opening_balance", ["class" => "form-control validate-float", "required" => true]); ?>
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