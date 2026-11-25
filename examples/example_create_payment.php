<?php
require_once 'config.php';

$mixplatConfiguration = new \MixplatClient\Configuration();
$mixplatConfiguration->projectId = $projectId;
$mixplatConfiguration->apiKey = $apiKey;

$httpClient = new \MixplatClient\HttpClient\SimpleHttpClient();

$mixplatClient = new \MixplatClient\MixplatClient();
$mixplatClient->setConfig($mixplatConfiguration);
$mixplatClient->setHttpClient($httpClient);

$apiRequest = new \MixplatClient\Method\CreatePayment();

$apiRequest->amount             = 10000;
$apiRequest->description        = 'Тестовая оплата';
$apiRequest->merchantFields     = array('test_field' => 'test_value');
$apiRequest->userEmail          = 'user@mail.ru';
$apiRequest->userName           = 'Константин Константинопольский';
$apiRequest->userPhone          = $successStatusUserPhone;
$apiRequest->paymentMethod      = \MixplatClient\MixplatVars::PAYMENT_METHOD_MOBILE;
$apiRequest->test               = 1;
// Чек для ФФД 1.05.
$apiRequest->items = array(
    array(
        'name' => 'Блокнот',
        'quantity' => 1,
        'sum' => 10000,
        'unit' => 'шт.',
        'payment_object' => 'commodity',
    ),
);

try {
    $response = $mixplatClient->request($apiRequest);
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

