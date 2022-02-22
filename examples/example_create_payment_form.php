<?php
require_once 'config.php';

$mixplatConfiguration = new \MixplatClient\Configuration();
$mixplatConfiguration->projectId = $projectId;
$mixplatConfiguration->apiKey = $apiKey;

$httpClient = new \MixplatClient\HttpClient\SimpleHttpClient();

$mixplatClient = new \MixplatClient\MixplatClient();
$mixplatClient->setConfig($mixplatConfiguration);
$mixplatClient->setHttpClient($httpClient);

$createPayment = new \MixplatClient\Method\CreatePaymentForm();

$ourPaymentId = rand(1000, 10000);

$createPayment->test = 1;
$createPayment->merchantPaymentId = $ourPaymentId;
$createPayment->paymentMethod = \MixplatClient\MixplatVars::PAYMENT_METHOD_CARD;

$createPayment->amount = 2100;
$createPayment->merchantFields = array('pid' => $ourPaymentId);
$createPayment->description = 'Оплата через форму';
$createPayment->billingType = \MixplatClient\MixplatVars::BILLING_TYPE_GOOGLE_PAY;

try {
    $response = $mixplatClient->request($createPayment);
} catch (\MixplatClient\MixplatException $errorException) {
    echo 'Error: ' . $errorException->getMessage();
    $response = null;
}

print_r($response);

if ($response && $response['result'] === \MixplatClient\MixplatVars::RESULT_OK) {
    /* process payment... */
    /* redirect to $response['redirect_url'] */
    /* header('Location: ' .  $response['redirect_url'] , true, 303); */

} else {
    /* error */
}

