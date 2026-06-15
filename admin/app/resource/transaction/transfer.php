<?php

use App\Auth;
use App\Form;
use App\Model\PaymentGateway;
use App\Model\Setting;
use App\Model\Transaction;
use HardeepVicky\QueryBuilder\Condition;
use HardeepVicky\QueryBuilder\QuerySelect;
use HardeepVicky\QueryBuilder\Table;
use PhonePe\payments\v2\models\request\builders\StandardCheckoutPayRequestBuilder;
use PhonePe\payments\v2\standardCheckout\StandardCheckoutClient;

require_once './app/model/BaseModel.php';
require_once './app/model/PaymentGateway.php';
require_once './app/model/Setting.php';
require_once './app/model/Transaction.php';
require_once './app/include/Form.php';


$model = new Transaction();

$condition = Condition::init("AND")->add("user_id", $auth->user['id']);

if (isset($_GET['form_data']['user_id']))
{
    $condition->add("user_id", $_GET['form_data']['user_id']);
}

if (isset($_GET['form_data']['payment_gateway_id']))
{
    $condition->add("payment_gateway_id", $_GET['form_data']['payment_gateway_id']);
}

if (isset($_GET['form_data']['status']))
{
    $condition->add("status", $_GET['form_data']['status']);
}

$qs = new QuerySelect(new Table($model->getTable()));
$qs->setWhere($condition);
$qs->order("id", "DESC");
$qs->setOffset(0);
$qs->setLimit(5);

$records = $model->findQuery($qs);

$model->user_id($records);
$model->payment_gateway_id($records);


$form = new Form($model);
$payment_gateway_model = new PaymentGateway();

if (isset($_POST['form_data']))
{
    $payment_gateway = $payment_gateway_model->find([], ["id" => $_POST['form_data']['payment_gateway_id']]);

    $payment_gateway = $payment_gateway[0];

    $setting = new Setting();
    $setting_list = $setting->findValue(["phonepe_client_id", "phonepe_client_version", "phonepe_client_secret", "phonepe_env"]);

    $auth = new Auth();
    $_POST['form_data']['user_id'] = $auth->user['id'];
    $_POST['form_data']['status'] = 'pending';
    if ($model->insert($_POST['form_data']))
    {
        if ($payment_gateway['code'] == PaymentGateway::PHONEPE)
        {
            $_SESSION['phonepe_order_id'] = $model->id;

            $amount = $_POST['form_data']['amount'] * 100; // Amount in paisa (e.g., 1000 = ₹10.00)
            $message = "Your order details";

            $redirectUrl = BASE_URL . "index.php?r=transaction/phonepay_redirect"; // URL to which PhonePe will redirect after payment

            $client = StandardCheckoutClient::getInstance(
                $setting_list['phonepe_client_id'],
                $setting_list['phonepe_client_version'],
                $setting_list['phonepe_client_secret'],
                $setting_list['phonepe_env']
            );

            $payRequest = StandardCheckoutPayRequestBuilder::builder()
                ->merchantOrderId($_SESSION['phonepe_order_id'])
                ->amount($amount)
                ->redirectUrl($redirectUrl)
                ->message($message)
                ->build();

            try {
                $payResponse = $client->pay($payRequest);

                // Handle the response
                if ($payResponse->getState() === "PENDING") {
                    // Redirect the user to the PhonePe payment page
                    header("Location: " . $payResponse->getRedirectUrl());
                    exit();
                } else {
                    // Handle the error (e.g., display an error message)
                    echo "Payment initiation failed: " . $payResponse->getState();
                }
            } catch (\PhonePe\common\exceptions\PhonePeException $e) {
                // Handle exceptions (e.g., log the error)
                echo "Error initiating payment: " . $e->getMessage();
            }
        }
        else if ($payment_gateway['code'] == PaymentGateway::ISG)
        {
            $_SESSION['isg_order_id'] = $model->id;

            $amount = $_POST['form_data']['amount'] * 100; 

            $gatewayUrl = ISG_GATEWAYURL; // Replace with Live URL later

            $redirectUrl = BASE_URL . "index.php?r=transaction/isg_redirect";

            // 2. Transaction Parameters
            $params = [
                'Version'     => '1.0',
                'TxnRefNo'    => $_SESSION['isg_order_id'], // Unique alphanumeric transaction reference
                'Amount'      => $amount,                            // Amount in smallest currency unit (e.g., Rs 101.20 = 10120)
                'PassCode'    => ISG_PASSCODE,                         // Provided by ISGPay
                'BankId'      => ISG_BANKID,                           // Provided by ISGPay
                'TerminalId'  => ISG_TERMINALID,                         // Provided by ISGPay
                'MerchantId'  => ISG_MERCHANTID,                  // Provided by ISGPay
                'MCC'         => ISG_MCC,                             // Merchant Category Code
                'Currency'    => '356',                              // 356 for INR
                'TxnType'     => 'Pay',
                'ReturnURL'   => $redirectUrl, // Your callback URL
                'OrderInfo'   => $_SESSION['isg_order_id']
            ];

            // 3. Generate SHA-256 Signature
            // ISGPay requires sorting the request parameters alphabetically or concatenating them in a specific strict order 
            ksort($params); 

            $signatureString = "";
            foreach ($params as $key => $value) {
                $signatureString .= $value;
            }
            // Append your secure hash secret at the end of the payload string
            $signatureString .= ISG_SECURE_SECRET; 

            // Generate secure hash signature
            $secureHash = hash("sha256", $signatureString);
            $params['SecureHash'] = $secureHash;
            ?>
            
            <!DOCTYPE html>
            <html>
            <head>
                <title>Redirecting to Payment Gateway...</title>
            </head>
            <body onload="document.forms['isgpay_form'].submit();">
                <h3>Please wait while we redirect you to the secure payment page...</h3>
                <form name="isgpay_form" action="<?php echo $gatewayUrl; ?>" method="POST">
                    <?php
                    foreach ($params as $name => $value) {
                        echo '<input type="hidden" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '">';
                    }
                    ?>
                </form>
            </body>
            </html>
            <?php
                exit();
        }
    }
    else
    {
        Session::writeFlash("fail", "Fail To Save");
    }
}



$payment_gateway_list = $payment_gateway_model->findList("id", "name", ["is_active" => 1]);

require_once './app/resource/layout/main/head.php' 
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Transction</h4>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <form method="post">                           
                            <div class="mb-3">
                                <?= $form->label("payment_gateway", ["class" => "form-label"]); ?>
                                <?= $form->input("payment_gateway_id", [
                                    "class" => "form-control js-choice", 
                                    "type" => "select", 
                                    "list" => $payment_gateway_list,
                                    "required" => true
                                    ]); 
                                ?>
                            </div>
                            <div class="mb-3">
                                <?= $form->label("amount", ["class" => "form-label"]); ?>
                                <?= $form->input("amount", ["class" => "form-control", "required" => true]); ?>
                            </div>                            
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary w-md">Submit</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6 col-lg-8">
                        Last 5 Transctions
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Payment Gateway</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Datetime</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($records as $record):?>
                                    <tr>
                                        <td><?= $record['payment_gateway_id']['name'] ?? ""  ?></td>
                                        <td><?= $record['amount'] ?></td>
                                        <td><?= isset(Transaction::STATUS_LIST[$record['status']]) ? Transaction::STATUS_LIST[$record['status']] : $record['status'] ?></td>
                                        <td>
                                            <?=  DateUtility::getDate($record['created_at'], DateUtility::DATETIME_OUT_FORMAT) ?>
                                        </td>
                                    </tr>                    
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
</div>
<?php require_once './app/resource/layout/main/foot.php' ?>