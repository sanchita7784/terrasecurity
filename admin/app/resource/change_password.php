<?php

use App\Form;
use App\Auth;

require_once './app/model/BaseModel.php';
require_once './app/model/User.php';
require_once './app/include/Form.php';

$userModel = new App\Model\User();

$form = new Form($userModel);

$auth = new Auth();

if (isset($_POST['form_data']))
{
    $records = $userModel->find([], [
        "username" => $auth->user['username'],
    ]);
    
    if (password_verify($_POST['form_data']['old_password'], $records[0]['password']))
    {
        if ($_POST['form_data']['new_password'] == $_POST['form_data']['confirm_password'])
        {
            $userModel->id = $records[0]['id'];
            $result = $userModel->update([
                "password" => $_POST['form_data']['new_password'],
                "confirm_password" => $_POST['form_data']['confirm_password']
            ]);

            if ($result)
            {
                Session::writeFlash("success", "Password has been changed.");
            }
            else
            {
                Session::writeFlash("fail", "Fail To Change Password.");
            }            
        }
        else
        {
            Session::writeFlash("fail", "New Password and Confirm Password does not match.");
        }    
    }
    else
    {
        Session::writeFlash("fail", "Wrong Old Password");        
    }
}
?>
<?php
require_once './app/resource/layout/main/head.php'
?>

<form method="post">
    <div class="row">
        <div class="col-6">
            <div class="card">               
                <div class="card-body p-4">
                    <div class="mb-3">
                        <?= $form->label("old_password", ["class" => "form-label"]); ?>
                        <?php $name = 'old_password'; ?>
                        <?= $form->input("form_data[$name]", ["class" => "form-control", "type" => "password"]); ?>
                    </div>

                    <div class="mb-3">
                        <?= $form->label("new_password", ["class" => "form-label"]); ?>
                        <?php $name = 'new_password'; ?>
                        <?= $form->input("form_data[$name]", ["class" => "form-control", "type" => "password"]); ?>
                    </div>

                    <div class="mb-3">
                        <?= $form->label("confirm_password", ["class" => "form-label"]); ?>
                        <?php $name = 'confirm_password'; ?>
                        <?= $form->input("form_data[$name]", ["class" => "form-control", "type" => "password"]); ?>
                    </div>
                </div>

                <div class="card-footer p-2 text-center">
                    <button type="submit" class="btn btn-primary w-md">Submit</button>
                </div>
            </div>
        </div>
    </div>
    
</form>

<?php require_once './app/resource/layout/main/foot.php' ?>
