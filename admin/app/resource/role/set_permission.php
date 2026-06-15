<?php

use App\Cache;
use App\Form;
use App\Model\Role;

require_once './app/model/BaseModel.php';
require_once './app/model/Role.php';
require_once './app/model/Permission.php';
require_once './app/include/Form.php';

$model = new App\Model\Permission();

$form = new Form($model);

if (isset($_POST['form_data']))
{
    // d($_POST['form_data']); exit;
    $role_id = $_POST['form_data']['role_id'];

    $model->mysql->query("DELETE FROM permission where role_id=" . $role_id);

    foreach($_POST['form_data']['permission'] as $permission_link => $val)
    {
        $model->insert([
            "role_id" => $_POST['form_data']['role_id'],
            "permission_link" => $permission_link
        ]);
    }

    $cache = new Cache("auth");
    $cache->delete("allowedPermissions.$role_id");

    Session::writeFlash("success", "Permission has been saved.");
    redirect("role/set_permission");
}

$role = new Role();
$role_list = $role->findListCache("id", "name");
require_once './app/resource/layout/main/head.php';
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Permission</h4>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-lg-6">
                        <form method="post">                           
                            <div class="mb-3">
                                <?= $form->label("role", ["class" => "form-label"]); ?>
                                <?= $form->input("role_id", [
                                    "id" => "role_id",
                                    "class" => "form-control js-choice",
                                    "type" => "select",
                                    "list" => $role_list,
                                    "empty" => true
                                ]); ?>
                            </div>
                            <div id="permission_block">

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

<script>
    $(function(){
        $("#role_id").change(function(){
            var role_id = $(this).val();
            if (role_id)
            {
                $("#permission_block").load("<?=  url_without_query_params("role/ajax_get_permission") ?>" + "&role_id=" + role_id, function(){
                    
                });
            }
            else
            {
                $("#permission_block").html("");
            }

            return false;
        });
    });
</script>
<?php require_once './app/resource/layout/main/foot.php' ?>