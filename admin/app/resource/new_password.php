<?php

use App\Form;
use HardeepVicky\QueryBuilder\Condition;
use HardeepVicky\QueryBuilder\QuerySelect;
use HardeepVicky\QueryBuilder\Table;

require_once './app/include/Form.php';

$user = new App\Model\User();

$form = new Form($user);

if (isset($_GET['uuid']))
{
    $user_records = $user->find([], ["uuid" => $_GET['uuid']]);

    if (empty($user_records))
    {
        die("Invalid uuid");
    }
    $user_record = $user_records[0];
}
else
{
    die("Invalid URL");
}


if (isset($_POST['form_data']))
{
    // d($_POST['form_data']); exit;

    $user->id = $user_record['id'];

    if ($user->update($_POST['form_data']))
    {
        Session::writeFlash("success", "Password Changed successfully");
        redirect("login");
    }
    else
    {
        Session::writeFlash("fail", "Fail To Save");
    }
}


require_once './app/resource/layout/login/head.php';
?>
<div class="w-100">
    <div class="d-flex flex-column h-100">
        <div class="mb-4 mb-md-5 text-center">
            <a href="index.html" class="d-block auth-logo">
                <img src="assets/images/logo-sm.svg" alt="" height="28"> <span class="logo-txt">Terra Ventures</span>
            </a>
        </div>
        <div class="auth-content my-auto">
            <?php if (Session::hasFlash("success")): ?>
            <div class="alert alert-success" role="alert">
                <?= Session::readFlash("success") ?>
            </div>
            <?php endif; ?>

            <?php if (Session::hasFlash("fail")): ?>
            <div class="alert alert-danger" role="alert">
                <?= Session::readFlash("fail") ?>
            </div>
            <?php endif; ?>
            
            <form class="mt-4 pt-2" method="post">
                <div class="mb-3">
                    <?= $form->label("password", ["class" => "form-label"]); ?>
                    <?= $form->input("password", ["class" => "form-control", "type" => "password", "required" => true]); ?>
                </div>
                <div class="mb-3">
                    <?= $form->label("confirm_password", ["class" => "form-label"]); ?>
                    <?= $form->input("confirm_password", ["class" => "form-control", "type" => "password", "required" => true]); ?>
                </div>
                
                <div class="mb-3">
                    <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">Submit</button>
                </div>
            </form>

            <div class="mt-5 text-center">
                <p class="text-muted mb-0">Don't have an account ? <a href="<?= url("register") ?>" class="text-primary fw-semibold"> Signup now </a> </p>
            </div>
        </div>
    </div>
</div>

<?php 
require_once './app/resource/layout/login/foot.php';
?>