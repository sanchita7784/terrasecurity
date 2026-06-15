<?php

use App\Form;
use HardeepVicky\QueryBuilder\Condition;
use HardeepVicky\QueryBuilder\QuerySelect;
use HardeepVicky\QueryBuilder\Table;

require_once './app/model/BaseModel.php';
require_once './app/model/Setting.php';
require_once './app/include/Form.php';

$setting = new App\Model\Setting();

$form = new Form($setting);

if (isset($_POST['form_data']))
{
    $saved = true;
    foreach($_POST['form_data'] as $k => $v)
    {
        $qs = new QuerySelect(new Table($setting->getTable()));
        $qs->setWhere(Condition::init("AND")->add("name", $k));
        $records = $setting->findQuery($qs);

        $data = [
            "name" => $k,
            "value" => $v
        ];

        if ($records)
        {
            $setting->id = $records[0]['id'];
            if (!$setting->update($data))
            {
                $saved = false;
                Session::writeFlash("fail", "Fail To Save");
                break;
            }
        }
        else
        {
            if (!$setting->insert($data))
            {
                $saved = false;
                Session::writeFlash("fail", "Fail To Save");
                break;
            }
        }
        
        if ($saved)
        {
            Session::writeFlash("success", "Settings has been Saved");
        }
    }
}

$records = $setting->find();

$list = [];

foreach($records as $record)
{
    $list[$record['name']] = $record['value'];
}


require_once './app/resource/layout/main/head.php'
?>

<form method="post">
    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Phonepe</h4>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <?= $form->label("phonepe_client_id", ["class" => "form-label"]); ?>
                        <?php $name = 'phonepe_client_id'; ?>
                        <?= $form->input("form_data[$name]", ["class" => "form-control", 'value' => $list[$name] ?? "" ]); ?>
                    </div>
                    <div class="mb-3">
                        <?= $form->label("phonepe_client_version", ["class" => "form-label"]); ?>
                        <?php $name = 'phonepe_client_version'; ?>
                        <?= $form->input("form_data[$name]", ["class" => "form-control", 'value' => $list[$name] ?? "" ]); ?>
                    </div>
                    <div class="mb-3">
                        <?= $form->label("phonepe_client_secret", ["class" => "form-label"]); ?>
                        <?php $name = 'phonepe_client_secret'; ?>
                        <?= $form->input("form_data[$name]", ["class" => "form-control", 'value' => $list[$name] ?? "" ]); ?>
                    </div>
                    <div class="mb-3">
                        <?= $form->label("phonepe_client_secret", ["class" => "form-label"]); ?>
                        <?php $name = 'phonepe_env'; ?>
                        <?= $form->input("form_data[$name]", ["class" => "form-control", 'value' => $list[$name] ?? "" ]); ?>
                        <div class="help-block">
                            UAT for Sandbox, PRODUCTION for live
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">ISG Pay</h4>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <?= $form->label("enc_key", ["class" => "form-label"]); ?>
                        <?php $name = 'isg_enc_key'; ?>
                        <?= $form->input("form_data[$name]", ["class" => "form-control", 'value' => $list[$name] ?? "" ]); ?>
                    </div>
                    <div class="mb-3">
                        <?= $form->label("secure_secret", ["class" => "form-label"]); ?>
                        <?php $name = 'isg_secure_secret'; ?>
                        <?= $form->input("form_data[$name]", ["class" => "form-control", 'value' => $list[$name] ?? "" ]); ?>
                    </div>                  
                    <div class="mb-3">
                        <?= $form->label("passcode", ["class" => "form-label"]); ?>
                        <?php $name = 'isg_passcode'; ?>
                        <?= $form->input("form_data[$name]", ["class" => "form-control", 'value' => $list[$name] ?? "" ]); ?>
                    </div>
                    <div class="mb-3">
                        <?= $form->label("merchant_id", ["class" => "form-label"]); ?>
                        <?php $name = 'isg_merchant_id'; ?>
                        <?= $form->input("form_data[$name]", ["class" => "form-control", 'value' => $list[$name] ?? "" ]); ?>
                    </div>
                    <div class="mb-3">
                        <?= $form->label("bank_id", ["class" => "form-label"]); ?>
                        <?php $name = 'isg_bank_id'; ?>
                        <?= $form->input("form_data[$name]", ["class" => "form-control", 'value' => $list[$name] ?? "" ]); ?>
                    </div>
                    <div class="mb-3">
                        <?= $form->label("terminal_id", ["class" => "form-label"]); ?>
                        <?php $name = 'isg_terminal_id'; ?>
                        <?= $form->input("form_data[$name]", ["class" => "form-control", 'value' => $list[$name] ?? "" ]); ?>
                    </div>
                    <div class="mb-3">
                        <?= $form->label("mcc", ["class" => "form-label"]); ?>
                        <?php $name = 'isg_mcc'; ?>
                        <?= $form->input("form_data[$name]", ["class" => "form-control", 'value' => $list[$name] ?? "" ]); ?>
                    </div>                    
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">AWS</h4>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <?= $form->label("aws_access_key_id", ["class" => "form-label"]); ?>
                        <?php $name = 'aws_access_key_id'; ?>
                        <?= $form->input("form_data[$name]", ["class" => "form-control", 'value' => $list[$name] ?? "" ]); ?>
                    </div>
                    <div class="mb-3">
                        <?= $form->label("aws_secret_access_key", ["class" => "form-label"]); ?>
                        <?php $name = 'aws_secret_access_key'; ?>
                        <?= $form->input("form_data[$name]", ["class" => "form-control", 'value' => $list[$name] ?? "" ]); ?>
                    </div>
                    <div class="mb-3">
                        <?= $form->label("aws_default_region", ["class" => "form-label"]); ?>
                        <?php $name = 'aws_default_region'; ?>
                        <?= $form->input("form_data[$name]", ["class" => "form-control", 'value' => $list[$name] ?? "" ]); ?>
                    </div>                   
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">General</h4>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <?= $form->label("email_from_address", ["class" => "form-label"]); ?>
                        <?php $name = 'email_from_address'; ?>
                        <?= $form->input("form_data[$name]", ["class" => "form-control", 'value' => $list[$name] ?? "" ]); ?>
                    </div>                                   
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary w-md">Submit</button>
    </div>
</form>

<?php require_once './app/resource/layout/main/foot.php' ?>