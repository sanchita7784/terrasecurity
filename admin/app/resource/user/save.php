<?php

use App\Form;
use App\Model\Role;

require_once './app/model/BaseModel.php';
require_once './app/model/User.php';
require_once './app/model/Role.php';
require_once './app/model/Location.php';
require_once './app/include/Form.php';

$user = new App\Model\User();

$form = new Form($user);

$location = new App\Model\Location();

$location_list = $location->findListCache("id", "name");

if (isset($_POST['form_data']))
{
    if (!isset($_POST['form_data']['is_active']))
    {
        $_POST['form_data']['is_active'] = 0;
    }

    
    if (isset($_GET['id']))
    {
        $user->id = $_GET['id'];
        if ($user->update($_POST['form_data']))
        {
            Session::writeFlash("success", "User has been updated.");
            redirect("user/summary");
        }
        else
        {
            Session::writeFlash("fail", "Fail To Update.");
        }
    }
    else
    {
        if ($user->insert($_POST['form_data']))
        {
            Session::writeFlash("success", "User has been Saved");
            redirect("user/summary");
        }
        else
        {
            Session::writeFlash("fail", "Fail To Save");
        }
    }
}

$role = new Role();
$role_list = $role->findListCache("id", "name");

require_once './app/resource/layout/main/head.php' 
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">User Form</h4>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-lg-6">
                        <form method="post">
                            <div class="mb-3">
                                <?= $form->label("role", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("role_id", [
                                    "class" => "form-control select2",
                                    "type" => "select",
                                    "list" => $role_list,
                                    "empty" => true
                                ]); ?>
                            </div>
                            <div class="mb-3">
                                <?= $form->label("name", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("name", ["class" => "form-control"]); ?>
                            </div>
                            <div class="mb-3">
                                <?= $form->label("username", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("username", ["class" => "form-control"]); ?>
                            </div>
                            <?php if (!isset($_GET['id'])) : ?>
                            <div class="mb-3">
                                <?= $form->label("password", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("password", ["class" => "form-control", "type" => "password"]); ?>
                            </div>
                            <div class="mb-3">
                                <?= $form->label("confirm_password", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("confirm_password", ["class" => "form-control", "type" => "password"]); ?>
                            </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <?= $form->label("email", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("email", ["class" => "form-control", "type" => "email"]); ?>
                            </div>
                            <div class="mb-3">
                                <?= $form->label("mobile", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("mobile", ["class" => "form-control", "type" => "text"]); ?>
                            </div>
                            <div class="mb-3">
                                <?= $form->label("working location", ["class" => "form-label"]); ?>
                                <?= $form->input("location_id", [
                                    "class" => "form-control select",
                                    "type" => "select",
                                    "list" => $location_list,
                                    "empty" => true
                                ]); ?>
                            </div>
                            <label class="form-check mb-3">
                                <?= $form->input("is_active", ["class" => "form-check-input", "type" => "checkbox", "value" => "1"]); ?>
                                <span>Active</span>
                            </label>
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