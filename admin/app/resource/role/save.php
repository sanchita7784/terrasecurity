<?php

use App\Form;

require_once './app/model/BaseModel.php';
require_once './app/model/Role.php';
require_once './app/include/Form.php';

$model = new App\Model\Role();

$form = new Form($model);

if (isset($_POST['form_data']))
{
    $_POST['form_data']['is_active'] = isset($_POST['form_data']['is_active']) ? 1 : 0;
    
    if (isset($_GET['id']))
    {
        $model->id = $_GET['id'];
        if ($model->update($_POST['form_data']))
        {
            Session::writeFlash("success", "Record has been updated.");
            redirect("role/summary");
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
            Session::writeFlash("success", "Record has been Saved");
            redirect("role/summary");
        }
        else
        {
            Session::writeFlash("fail", "Fail To Save");
        }
    }
}

require_once './app/resource/layout/main/head.php' 
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Role Form</h4>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-lg-6">
                        <form method="post">                           
                            <div class="mb-3">
                                <?= $form->label("name", ["class" => "form-label"]); ?>
                                <?= $form->input("name", ["class" => "form-control"]); ?>
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