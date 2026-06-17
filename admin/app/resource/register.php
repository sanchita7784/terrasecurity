<?php

use App\Auth;
use App\EmailHelper;
use App\Form;
use App\Model\Role;

require_once './app/include/Form.php';
require_once './app/include/EmailHelper.php';

$model = new App\Model\User();

$form = new Form($model);

$auth = new Auth();

$role = new Role();

$role_record = $role->find([], ["name" => "user"]);

if (empty($role_record))
{
    die("user role not found in database");
}
$role_record = $role_record[0];


if (isset($_POST['form_data']))
{
    $user_form_data = $_POST['form_data'];
    $user_form_data['uuid'] = time();
    $user_form_data['role_id'] = $role_record['id'];
    // d($user_form_data); exit;

    if ($model->insert($user_form_data))
    {
        $verify_link = url("register_verify", [
            "uuid" => $user_form_data['uuid']
        ]);

        $html = view("./app/email/register", [
            "verify_link" => $verify_link
        ]);

        $emailHelper = new EmailHelper();
        $emailHelper->send($user_form_data['email'], "Verify Account", $html);

        Session::writeFlash("success", "Verification Email has been sent");
        redirect("login");
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
            
            <form method="post">                
                <div class="mb-3">
                    <?= $form->label("name", ["class" => "form-label"]); ?>
                    <?= $form->input("name", ["class" => "form-control", "required" => true]); ?>
                </div>
                <div class="mb-3">
                    <?= $form->label("username", ["class" => "form-label"]); ?>
                    <?= $form->input("username", ["class" => "form-control", "required" => true]); ?>
                </div>
                <?php if (!isset($_GET['id'])) : ?>
                <div class="mb-3">
                    <?= $form->label("password", ["class" => "form-label"]); ?>
                    <?= $form->input("password", ["class" => "form-control", "type" => "password", "required" => true]); ?>
                </div>
                <div class="mb-3">
                    <?= $form->label("confirm_password", ["class" => "form-label"]); ?>
                    <?= $form->input("confirm_password", ["class" => "form-control", "type" => "password", "required" => true]); ?>
                </div>
                <?php endif; ?>
                <div class="mb-3">
                    <?= $form->label("email", ["class" => "form-label"]); ?>
                    <?= $form->input("email", ["class" => "form-control", "type" => "email", "required" => true]); ?>
                </div>
                <div class="mb-3">
                    <?= $form->label("mobile", ["class" => "form-label"]); ?>
                    <?= $form->input("mobile", ["class" => "form-control", "type" => "text", "required" => true]); ?>
                </div>
                <div class="mb-3">
                    <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">Submit</button>
                </div>
            </form>
        </div>

        <div class="mt-5 text-center">
            <p class="text-muted mb-0">have an account ? <a href="<?= url("login") ?>" class="text-primary fw-semibold"> Login now </a> </p>
        </div>
    </div>
</div>

<script>
    $(function()
    {
        $(".timezone").val(getTimeZone());
    });
</script>
<?php 
require_once './app/resource/layout/login/foot.php';
?>