<?php
// 1. Capture the payment response parameters
$response_data = $_POST;

d($response_data); // For debugging, remove in production

$hashSecret = ISG_SECURE_SECRET;
$incomingHash = isset($response_data['SecureHash']) ? $response_data['SecureHash'] : '';

// Remove SecureHash parameter from data array to calculate check-hash accurately
unset($response_data['SecureHash']);

// 2. Recalculate Hash for verification
ksort($response_data);
$checksumString = "";
foreach ($response_data as $key => $value) {
    $checksumString .= $value;
}
$checksumString .= $hashSecret;
$calculatedHash = hash("sha256", $checksumString);

// 3. Verify Signature Authenticity
if (strcasecmp($incomingHash, $calculatedHash) === 0) {
    
    // Check payment status code ('00' usually represents success in ISGPay)
    $responseCode = isset($response_data['ResponseCode']) ? $response_data['ResponseCode'] : '';
    $txnRefNo     = isset($response_data['TxnRefNo']) ? $response_data['TxnRefNo'] : '';
    $amount       = isset($response_data['Amount']) ? $response_data['Amount'] : '';
    
    if ($responseCode === "00") {
        // Payment Success!
        // TODO: Update your database using prepared statements, update order status, clear cart.
        echo "<h3>Payment Successful! Thank you. Transaction ID: " . htmlspecialchars($txnRefNo) . "</h3>";
    } else {
        // Payment failed or was canceled by user
        $message = isset($response_data['Message']) ? $response_data['Message'] : 'Unknown Error';
        echo "<h3>Payment Failed. Reason: " . htmlspecialchars($message) . "</h3>";
    }
    
} else {
    // Security alert! The response data might have been tampered with.
    echo "<h3>Security Error: Digital Signature verification failed.</h3>";
}
?>