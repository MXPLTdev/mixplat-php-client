<?php
require_once 'config.php';

$mixplatConfiguration = new \MixplatClient\Configuration();
$mixplatConfiguration->projectId = $projectId;
$mixplatConfiguration->apiKey = $apiKey;

$mixplatCallback = new \MixplatClient\MixplatCallback();

$notify = $mixplatCallback
    ->init()
    ->getNotify();

if (!$notify) {
    $mixplatCallback->returnError(\MixplatClient\MixplatVars::RESULT_ERROR_INVALID_REQUEST, 'Неверный запрос');
    return;
}

if (!$notify->checkSignature($mixplatConfiguration)) {
    $mixplatCallback->returnError(\MixplatClient\MixplatVars::RESULT_ERROR_SIGNATURE, 'Неверная подпись запроса');
    return;
}

if ($notify->request === \MixplatClient\MixplatVars::NOTIFY_REQUEST_PAYMENT_STATUS) {
    /* process payment status... */
} elseif ($notify->request === \MixplatClient\MixplatVars::NOTIFY_REQUEST_SMS) {
    /* process sms...*/
}

$mixplatCallback->returnSuccess();

