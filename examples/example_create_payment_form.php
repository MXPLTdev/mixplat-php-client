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

$apiRequest->amount             = 30000;
$apiRequest->description        = 'Тестовая оплата';
$apiRequest->paymentMethod      = \MixplatClient\MixplatVars::PAYMENT_METHOD_CARD;
$apiRequest->merchantFields     = array('test_field' => 'test_value');
$apiRequest->userEmail          = 'user@mail.ru';
$apiRequest->userName           = 'Константин Константинопольский';
$apiRequest->userPhone          = '79991234567';
$apiRequest->test               = 1;
// Чек для ФФД 1.2.
$apiRequest->items = array(
    array(
        'name' => 'Блокнот',
        'quantity' => 3,
        'sum' => 30000, // 3 шт. по 100 ₽
        'unit' => \MixplatClient\MixplatVars::ITEMS_UNIT_FFD12_PIECE,
        'payment_object' => \MixplatClient\MixplatVars::ITEMS_OBJECT_FFD12_COMMODITY,
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
    /* redirect to $response['redirect_url'] */
    // echo '<script>window.location.replace(' . json_encode($response['redirect_url'], JSON_HEX_TAG) . ');</script>';

} else {
    /* error */
}

