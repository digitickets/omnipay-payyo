<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Omnipay\Omnipay;

$paymentReference = $_GET['reference'] ?? (string)random_int(1000, 9999);
$paymentFile = __DIR__ . '/transaction-' . $paymentReference . '.json';

if (file_exists($paymentFile)) {
    $payment = json_decode(file_get_contents($paymentFile), true);
} else if (!isset($_GET['api_key'], $_GET['secret_key'], $_GET['merchant_id'])) {
    ?>
    <form method="get" enctype="application/x-www-form-urlencoded">
        <p><input type="text" name="api_key" placeholder="API Key: api_..."/></p>
        <p><input type="text" name="secret_key" placeholder="Secret Key: sec_..."/></p>
        <p><input type="number" name="merchant_id" placeholder="Merchant ID: 1234"/></p>
        <p><input type="submit" value="Go!"/></p>
    </form>
    <?php
    exit;
} else {
    $payment = [
        'api_key' => $_GET['api_key'],
        'secret_key' => $_GET['secret_key'],
        'merchant_id' => $_GET['merchant_id'],
    ];
    file_put_contents($paymentFile, json_encode($payment, JSON_PRETTY_PRINT));
}


// Now the testing can begins. Everything above was just keys management stuff ...


$gateway = Omnipay::create('Payyo');
$gateway->initialize([
    'apiKey' => $payment['api_key'],
    'secretKey' => $payment['secret_key'],
    'merchantId' => $payment['merchant_id'],
    'testMode' => true,
]);

// Customer returns
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'failure') {
        exit('Payment failed.');
    }
    
    $transactionId = $_GET['transaction_id'];
    $captureData = [
        'transactionReference' => $transactionId,
    ];
    $response = $gateway->completeAuthorize($captureData)->send();
    //$response = $gateway->completePurchase($captureData)->send();
    
    if ($response->isSuccessful()) {
        $response = $gateway->capture([
            'transactionReference' => $transactionId,
        ])->send();
        
        if ($response->isSuccessful()) {
            exit('Done! Transaction ID: ' . $response->getTransactionReference());
        }
    }

    exit('Failure: ' . $response->getMessage());
}

// Create payment page and redirect
$url = 'http://' . $_SERVER['HTTP_HOST'] . '?reference=' . $paymentReference;
$paymentPageData = [
    'transactionId' => '123456',
    'description' => '1x Book',
    'amount' => '10.00',
    'currency' => 'USD',
    'paymentMethods' => ['credit_card', 'direct_debit', 'twint'],
    'returnUrl' => $url . '&status=success',
    'cancelUrl' => $url . '&status=failure',
];

$response = $gateway->authorize($paymentPageData)->send();
//$response = $gateway->purchase($paymentPageData)->send();

if (!$response->isRedirect()) {
    exit('Failure: ' . $response->getMessage());
}

$response->redirect();
