<?php
require_once 'config.php';

$mixplatConfiguration = new \MixplatClient\Configuration();
$mixplatConfiguration->projectId = $projectId;
$mixplatConfiguration->apiKey = $apiKey;

$httpClient = new \MixplatClient\HttpClient\SimpleHttpClient();

$mixplatClient = new \MixplatClient\MixplatClient();
$mixplatClient->setConfig($mixplatConfiguration);
$mixplatClient->setHttpClient($httpClient);

$apiRequest = new \MixplatClient\Method\CreatePaymentForm();

$apiRequest->amount             = 300;
$apiRequest->description        = 'Тестовая оплата';
$apiRequest->paymentMethod      = \MixplatClient\MixplatVars::PAYMENT_METHOD_CARD;
$apiRequest->billingType        = \MixplatClient\MixplatVars::BILLING_TYPE_BANK_CARD;
$apiRequest->merchantFields     = ['test_field'=>'test_value'];
$apiRequest->userEmail          = 'user@mail.ru';
$apiRequest->userName           = 'Константин Константинопольский';
$apiRequest->userPhone          = '79991234567';
$apiRequest->test               = 0;
$apiRequest->items              = [
    ['name'=>'Название позиции','sum'=>100],
    ['name'=>'Еще одна позиция','sum'=>200]
];

try {
    $response = $mixplatClient->request($apiRequest);
} catch (\MixplatClient\MixplatException $errorException) {
    echo 'Error: ' . $errorException->getMessage();
    $response = null;
}

print_r($response);

if ($response && $response['result'] === \MixplatClient\MixplatVars::RESULT_OK) {
    /* process payment... */
    /* redirect to $response['redirect_url'] */
    /* echo "<script>window.location.replace('".$response['redirect_url']."');</script>";

} else {
    /* error */
}

