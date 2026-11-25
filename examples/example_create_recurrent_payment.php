<?php
require_once 'config.php';

$mixplatConfiguration = new \MixplatClient\Configuration();
$mixplatConfiguration->projectId = $projectId;
$mixplatConfiguration->apiKey = $apiKey;

$httpClient = new \MixplatClient\HttpClient\SimpleHttpClient();

$mixplatClient = new \MixplatClient\MixplatClient();
$mixplatClient->setConfig($mixplatConfiguration);
$mixplatClient->setHttpClient($httpClient);

$createPayment = new \MixplatClient\Method\CreateRecurrentPayment();

$ourPaymentId = (string) rand(1000, 10000);

$createPayment->merchantPaymentId = $ourPaymentId;
// Для проверки используйте recurrent_id своего тестового установочного платежа.
$createPayment->recurrentId = 'demo-recurrent';
$createPayment->amount = 3000;

$createPayment->items = array(
    array(
        'name' => 'Журнал "Огонек"',
        'sum' => 2000,
        'quantity' => 2, // 2 экземпляра по 10 ₽
        'unit' => 0,
        'vat' => \MixplatClient\MixplatVars::ITEMS_VAT_VAT20,
        'payment_method' => \MixplatClient\MixplatVars::ITEMS_METHOD_FULL_PAYMENT,
        'payment_object' => 1,
    ),
    array(
        'name' => 'Журнал "Мурзилка"',
        'sum' => 1000,
        'unit' => 0,
        'payment_object' => 1,
    ),
);

try {
    $response = $mixplatClient->request($createPayment);
} catch (\MixplatClient\MixplatException $errorException) {
    echo 'Error: ' . $errorException->getMessage();
    $response = null;
}

print_r($response);

if ($response && $response['result'] === \MixplatClient\MixplatVars::RESULT_OK) {
    /* process payment... */
    $paymentId = $response['payment_id'];

    /* a few minutes ago */
    sleep(3);

    /* get status */
    $getPaymentStatus = new \MixplatClient\Method\GetPaymentStatus();
    $getPaymentStatus->paymentId = $paymentId;
    $responseStatus = $mixplatClient->request($getPaymentStatus);

    print_r($responseStatus);

} else {
    /* error */
}

