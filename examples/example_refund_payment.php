<?php
require_once 'config.php';

$mixplatConfiguration = new \MixplatClient\Configuration();
$mixplatConfiguration->projectId = $projectId;
$mixplatConfiguration->apiKey = $apiKey;

$httpClient = new \MixplatClient\HttpClient\SimpleHttpClient();

$mixplatClient = new \MixplatClient\MixplatClient();
$mixplatClient->setConfig($mixplatConfiguration);
$mixplatClient->setHttpClient($httpClient);

$refundPayment = new \MixplatClient\Method\RefundPayment();

$ourRefundId = rand(10000, 100000);

$refundPayment->paymentId = 'rSKn9oKjFImiLElC7EbpViDGghIOUAvs';
$refundPayment->merchantRefundId = $ourRefundId;

try {
    $response = $mixplatClient->request($refundPayment);
} catch (\MixplatClient\MixplatException $errorException) {
    echo 'Error: ' . $errorException->getMessage();
    $response = null;
}

print_r($response);

if ($response && $response['result'] === \MixplatClient\MixplatVars::RESULT_OK) {
    /* process payment... */
    $refundId = $response['refund_id'];

    /* a few minutes ago */
    sleep(3);

    /* get status */
    $getRefundStatus = new \MixplatClient\Method\GetRefundStatus();
    $getRefundStatus->refundId = $refundId;
    $responseStatus = $mixplatClient->request($getRefundStatus);

    print_r($responseStatus);

} else {
    /* error */
}

