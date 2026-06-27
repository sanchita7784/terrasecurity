<?php

use App\Form;
use App\Model\LedgerAccount;

require_once './app/model/Company.php';
require_once './app/model/LedgerAccount.php';
require_once './app/model/States.php';

$model = new App\Model\Company();

$form = new Form($model);

if (isset($_POST['form_data']))
{
    if (isset($_GET['id']))
    {
        $model->id = $_GET['id'];
        if ($model->update($_POST['form_data']))
        {
            Session::writeFlash("success", "Record has been updated.");
            redirect("company/summary");
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
            redirect("company/summary");
        }
        else
        {
            Session::writeFlash("fail", "Fail To Save");
        }
    }
}

$state = new App\Model\States();

$state_list = $state->findListCache("id", "name");

require_once './app/resource/layout/main/head.php'
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Company Form</h4>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-lg-6">
                        <form method="post">
                            <div class="mb-3">
                                <?= $form->label("name", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("name", ["class" => "form-control", "required" => true]); ?>
                            </div>
                            <div class="mb-3">
                                <?= $form->label("gst_no", ["class" => "form-label"]); ?>
                                <?= $form->input("gst_no", ["class" => "form-control"]); ?>
                            </div>
                            <div class="mb-3">
                                <?= $form->label("owner_name", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("owner_name", ["class" => "form-control", "required" => true]); ?>
                            </div>
                            <div class="mb-3">
                                <?= $form->label("mobile", ["class" => "form-label"]); ?>
                                <?= $form->input("mobile", ["class" => "form-control validate-mobile"]); ?>
                            </div>
                            <div class="mb-3">
                                <?= $form->label("email", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("email", ["class" => "form-control validate-email", "required" => true]); ?>
                            </div>
                            <div class="mb-3">
                                <?= $form->label("address", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("address", ["class" => "form-control validate-email", "required" => true]); ?>
                            </div>
                            <div class="mb-3">
                                <?= $form->label("state", ["class" => "form-label", 'required' => true]); ?>
                                <?= $form->input("state_id", ["class" => "form-control select2", 
                                    "type" => "select",
                                    "list" => $state_list,
                                    "empty" => true,
                                    "required" => true,
                                ]); ?>
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