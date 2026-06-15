<?php

use App\Model\Setting;
use App\Model\Transaction;
use PhonePe\payments\v2\standardCheckout\StandardCheckoutClient;

require_once './app/model/Setting.php';
require_once './app/model/Transaction.php';

$setting = new Setting();
$setting_list = $setting->findValue(["phonepe_client_id", "phonepe_client_version", "phonepe_client_secret", "phonepe_env"]);

$transaction = new Transaction();

$client = StandardCheckoutClient::getInstance(
        $setting_list['phonepe_client_id'],
        $setting_list['phonepe_client_version'],
        $setting_list['phonepe_client_secret'],
        $setting_list['phonepe_env']
    );

try {
    $statusCheckResponse = $client->getOrderStatus($_SESSION['phonepe_order_id'], true);

    $transaction->id = $_SESSION['phonepe_order_id'];
    $transaction->update([
        "status" => $statusCheckResponse->state,
        "payment_gateway_response" => serialize($statusCheckResponse)
    ]);    

    if ($statusCheckResponse->state == "COMPLETED")
    {
        
    }
} catch (\PhonePe\common\exceptions\PhonePeException $e) {
    // Handle exceptions (e.g., log the error)
    echo "Error checking order status: " . $e->getMessage();
}

require_once './app/resource/layout/main/head.php'
?>


<div style="width: 300px; margin : 20px auto;">
    <?php if ($statusCheckResponse->state == "COMPLETED"): ?>    
    <div class="alert alert-success alert-dismissible fade show px-4 mb-0 text-center" role="alert">
        <i class="mdi mdi-check-all d-block display-4 mt-2 mb-3 text-success"></i>
        <h5 class="text-success">Success</h5>
        <p>Payment Transfer Done</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php elseif($statusCheckResponse->state == "FAILED"): ?>
        <div class="alert alert-danger alert-dismissible fade show px-4 mb-0 text-center" role="alert">
            <i class="mdi mdi-block-helper d-block display-4 mt-2 mb-3 text-danger"></i>
            <h5 class="text-danger">Error</h5>
            <p>Payment Transfer Failed</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?PHP endif; ?>
</div>

<?php require_once './app/resource/layout/main/foot.php' ?>