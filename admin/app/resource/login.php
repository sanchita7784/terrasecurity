<?php

use App\Auth;
use App\Cache;
use App\Form;


require_once './app/include/Form.php';

$user = new App\Model\User();

$form = new Form($user);

$auth = new Auth();

if (isset($_POST['form_data']))
{
    if ($auth->find($_POST['form_data']['username'], $_POST['form_data']['password']))
    {
        $cache = new Cache("auth");
        $cache->flush();
        $auth->setTimeZone($_POST['form_data']['timezone']);
        redirect("dashboard");
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
                <input type="hidden" name="form_data[timezone]" class="timezone" />
                <div class="mb-3">
                    <div class="mb-3">
                        <?= $form->label("username", ["class" => "form-label"]); ?>
                        <?= $form->input("username", ["class" => "form-control", "placeholder" => "Enter Username", "required" => true]); ?>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex align-items-start">
                        <div class="flex-grow-1">
                            <label class="form-label">Password</label>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="">
                                <a href="<?= url("forgot_password") ?>" class="text-muted">Forgot password?</a>
                            </div>
                        </div>
                    </div>

                    <div class="input-group auth-pass-inputgroup">
                        <?= $form->input("password", ["class" => "form-control", "placeholder" => "Enter Password"]); ?>
                        <button class="btn btn-light shadow-none ms-0" type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                    </div>
                </div>
                
                <div class="mb-3">
                    <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">Log In</button>
                </div>
            </form>

            <div class="mt-5 text-center">
                <p class="text-muted mb-0">Don't have an account ? <a href="<?= url("register") ?>" class="text-primary fw-semibold"> Signup now </a> </p>
            </div>
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