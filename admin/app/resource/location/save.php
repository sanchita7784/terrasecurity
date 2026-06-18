<?php

use App\Form;

require_once './app/model/BaseModel.php';
require_once './app/model/Location.php';
require_once './app/model/States.php';
require_once './app/model/Company.php';

$model = new App\Model\Location();

$form = new Form($model);

$state = new App\Model\States();

$state_list = $state->findListCache("id", "name");

$company = new App\Model\Company();

$company_list = $company->findListCache("id", "name");

if (isset($_POST['form_data']))
{
    if (isset($_GET['id']))
    {
        $model->id = $_GET['id'];
        if ($model->update($_POST['form_data']))
        {
            Session::writeFlash("success", "Record has been updated.");
            redirect("location/summary");
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
            redirect("location/summary");
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
                <h4 class="card-title">Location Form</h4>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-lg-6">
                        <form method="post">   
                            <div class="mb-3">
                                <?= $form->label("company", ["class" => "form-label"]); ?>
                                <?= $form->input("company_id", ["class" => "form-control select2", 
                                    "type" => "select",
                                    "list" => $company_list,
                                    "empty" => true,
                                ]); ?>
                            </div>                        
                            <div class="mb-3">
                                <?= $form->label("name", ["class" => "form-label"]); ?>
                                <?= $form->input("name", ["class" => "form-control"]); ?>
                            </div>   
                            <div class="mb-3">
                                <?= $form->label("address", ["class" => "form-label"]); ?>
                                <?= $form->input("address", ["class" => "form-control"]); ?>
                            </div>   
                            <div class="mb-3">
                                <?= $form->label("state", ["class" => "form-label"]); ?>
                                <?= $form->input("state_id", ["class" => "form-control select2", 
                                    "id" => "state_id",
                                    "type" => "select",
                                    "list" => $state_list,
                                    "empty" => true,
                                    "data-sr-cascade-target" =>"#city_id",
                                    "data-sr-cascade-url" => 'index.php?r=city/get_list&state_id={v}',
                                ]); ?>
                            </div>                           
                            <div class="mb-3">
                                <?= $form->label("city", ["class" => "form-label"]); ?>
                                <?= $form->input("city_id", ["class" => "form-control select2", 
                                    "id" => "city_id",
                                    "type" => "select",
                                    "list" => [],
                                    "empty" => true,
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

<script>
    $(function(){

        $("#state_id").cascade({
            onError: function(title, msg) {
                console.log([title, msg]);
                if (msg) {
                    $.events.onAjaxError(title, msg);
                }
            },
            beforeGet: function(src, url) {
                $.loader.init();
                $.loader.show();
                return url;
            },
            afterGet: function(src, dest, response) {
                $.loader.hide();
                return response;
            },
            afterValueSet: function(src, dest, val) {                
                dest.val(dest.attr("data-value"));                
            },
        }).trigger("change", {"pageLoad" : true});

    });
</script>
<?php require_once './app/resource/layout/main/foot.php' ?>