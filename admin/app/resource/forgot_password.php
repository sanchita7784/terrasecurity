<?php

use App\Cache;
use App\EmailHelper;
use App\Form;
use HardeepVicky\QueryBuilder\Condition;
use HardeepVicky\QueryBuilder\QuerySelect;
use HardeepVicky\QueryBuilder\Table;

require_once './app/include/Form.php';
require_once './app/include/EmailHelper.php';

$user = new App\Model\User();

$form = new Form($user);

if (isset($_POST['form_data']))
{
    $username_email = $_POST['form_data']['username'];    

    $qs = new QuerySelect(new Table("user"));
    $qs->setWhere(Condition::init("OR")->add("username", $username_email)->add("email", $username_email));

    $user_records = $user->findQuery($qs);

    if (empty($user_records))
    {
        Session::writeFlash("fail", "Invalid Username or Email");
    }
    else
    {
        $user_record = $user_records[0];
        $link = url("new_password", [
            "uuid" => $user_record['uuid']
        ]);

        $html = view("./app/email/forgot_password", [
            "link" => $link
        ]);

        $emailHelper = new EmailHelper();
        $emailHelper->send($user_record['email'], "Forgot Password", $html);

        Session::writeFlash("success", "Forgot Email has been sent");
        redirect($_GET['r']);
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
                    <div class="mb-3">
                        <?= $form->label("username / Email", ["class" => "form-label"]); ?>
                        <?= $form->input("username", ["class" => "form-control", "placeholder" => "Enter Username / Email", "required" => true]); ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">Send Email</button>
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