<?php

use App\Form;

require_once './app/model/BaseModel.php';
require_once './app/model/Employee.php';
require_once './app/model/States.php';
require_once './app/include/Form.php';

$model = new App\Model\Employee();

$form = new Form($model);

$state = new App\Model\States();

$state_list = $state->findListCache("id", "name");

if (isset($_POST['form_data']))
{
    if (isset($_POST['form_data']['image']) && $_POST['form_data']['image'])
    {
        $_POST['form_data']['image'] = FileUtility::moveFile($_POST['form_data']['image'], "storage/files/employee");
    }

    $_POST['form_data']['is_active'] = isset($_POST['form_data']['is_active']) ? 1 : 0;

    if (isset($_GET['id']))
    {
        $model->id = $_GET['id'];
        if ($model->update($_POST['form_data']))
        {
            Session::writeFlash("success", "Record has been updated.");
            redirect("employee/summary");
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
            redirect("employee/summary");
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
                <h4 class="card-title">Employee Form</h4>
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
                                <?= $form->label("address", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("address", ["class" => "form-control", "required" => true]); ?>
                            </div>
                            <div class="mb-3">
                                <?= $form->label("state", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("state_id", ["class" => "form-control select2",
                                    "id" => "state_id",
                                    "type" => "select",
                                    "list" => $state_list,
                                    "empty" => true,
                                    "required" => true,
                                    "data-sr-cascade-target" =>"#city_id",
                                    "data-sr-cascade-url" => 'index.php?r=city/get_list&state_id={v}',
                                ]); ?>
                            </div>
                            <div class="mb-3">
                                <?= $form->label("city", ["class" => "form-label", "required" => true]); ?>
                                <?= $form->input("city_id", ["class" => "form-control select2",
                                    "id" => "city_id",
                                    "type" => "select",
                                    "list" => [],
                                    "empty" => true,
                                    "required" => true
                                ]); ?>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <?= $form->label("Date Of Joining", ["class" => "form-label", "required" => true]); ?>
                                        <?= $form->input("doj", ["class" => "form-control date-picker" , 
                                            "data-date-end" => 0, 
                                            "required" => true
                                        ]); ?>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <?= $form->label("salary", ["class" => "form-label", "required" => true]); ?>
                                    <?= $form->input("salary", ["class" => "form-control validate-int", 
                                        "required" => true]); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <?= $form->label("mobile", ["class" => "form-label", "required" => true]); ?>
                                        <?= $form->input("mobile", ["class" => "form-control validate-mobile", 
                                            "required" => true
                                            ]); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2 mb-2">
                                <div class="col-sm-8">
                                    <?= $form->input("image", ["id" => "image", "class" => "form-control", "type" => "hidden"]); ?>
                                    <span id="modal_crop_opener" class="btn btn-secondary">Choose Photo</span>
                                </div>
                                <div class="col-sm-4">
                                    <?php if(isset($form->db_data['image']) && $form->db_data['image']): ?>
                                    <a id="profile_photo_block" class="fancybox" href="{{ FileUtility::get($form->db_data['image']) }}">
                                        <img class="img-thumbnail rounded-circle avatar-xl" src="{{ FileUtility::get($form->db_data['image']) }}" />
                                    </a>
                                    <?php endif; ?>
                                </div>
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


<style>
    #crop_image_area{
        width : 100%;
        max-width: 400px;
        height: 400px;
        margin: 0 auto 50px auto;
        border: 1px solid;
        padding: 2px;
    }

    #crop_image_preview{
        display: none;
        margin: 5px auto 5px auto;
        width: 200px;
        height: 200px;
    }
</style>
<div class="modal fade" id="modal-crop" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">            
            <div class="modal-header">
                <h5 class="modal-title">Upload & Crop Photo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body  text-center">
                <div id="crop_image_area" class="border-primary"></div>                
                
                <input type="file" name="file" id="crop_image_file" class="hidden" />
                <span id="crop_image_file_opener" class="btn btn-secondary">Choose Photo</span>

                <span id="crop_btn" class="btn btn-secondary">Crop</span>
                <div id="crop_image_preview">
                    <img/>
                </div>
            </div>
            <div class="modal-footer">
                <span id="crop_and_upload_btn" class="btn btn-primary">Crop & Upload</span>
                <div id="inline-loader" class="spinner-border text-primary m-1" style="display: none;">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>
    </div>
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

    $(function() {
        $("#modal_crop_opener").click(function(){
            $("#modal-crop").modal("show");
        });

        $("#crop_image_file_opener").click(function(){
            $("input#crop_image_file").click();
        });

        var $uploadCrop = $('#crop_image_area').croppie({
            viewport: {
                width: 200,
                height: 200,
                type: 'circle'
            },
            enableExif: true
        });

        function get_file_name()
        {
            var fullPath = $('input#crop_image_file').val();
            var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
            var filename = fullPath.substring(startIndex);
            if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
                filename = filename.substring(1);
            }

            return filename;
        }

        function readFile(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {

                    $uploadCrop.croppie('bind', {
                        url: e.target.result
                    }).then(function() {
                        
                    });

                }

                reader.readAsDataURL(input.files[0]);
            } else {
                alert("Sorry - you're browser doesn't support the FileReader API");
            }
        }

        $('input#crop_image_file').on('change', function() {
            readFile(this);
        });

        $("#crop_btn").click(function(){
            $uploadCrop.croppie('result', {
                type: 'canvas',
                size: 'viewport'
            }).then(function(resp) {                
                $("#crop_image_preview img").attr("src", resp);
                $("#crop_image_preview").show();
                $("#profile_photo_block").hide();
            });
        });

        $("#crop_and_upload_btn").click(function(){
            $uploadCrop.croppie('result', {
                type: 'canvas',
                size: 'viewport'
            }).then(function(resp) {                
                $("#crop_image_preview img").attr("src", resp);
                $("#crop_image_preview").show();
                $("#profile_photo_block").hide();

                var url = 'ajax_upload_base64.php';
                var data = {
                    "base64" : resp, "filename" : get_file_name()
                };

                $("#inline-loader").show();

                $.post(url, data, function(responseText){
                    console.log(responseText);
                    ajaxHandleResponse(url, responseText, function (response) {                        
                        $("#image").val(response['file']);
                        $("#inline-loader").hide();
                        $("#modal-crop").modal("hide");
                    });
                });
            });
        });

    });
</script>
<?php require_once './app/resource/layout/main/foot.php' ?>