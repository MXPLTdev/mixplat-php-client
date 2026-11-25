<?php
// Usage: php tests/openapi.php /path/to/openapi.json
require dirname(__DIR__) . '/src/autoload.php';

use MixplatClient\Configuration;
use MixplatClient\MixplatCallback;

function check($ok, $description)
{
    if (!$ok) {
        throw new RuntimeException($description);
    }
}

function camelCase($key)
{
    return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
}

if ($argc !== 2 || !is_file($argv[1])) {
    fwrite(STDERR, "Usage: php tests/openapi.php /path/to/openapi.json\n");
    exit(1);
}

$spec = json_decode(file_get_contents($argv[1]), true);
check(!empty($spec['paths']) && !empty($spec['webhooks']), 'Missing OpenAPI paths or webhooks');
error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

$config = new Configuration();
$config->apiKey = 'demo_secret'; // Signing secret from the public documentation examples.
$requestCount = 0;
foreach ($spec['paths'] as $path => $operation) {
    $class = 'MixplatClient\\Method\\' . ucfirst(camelCase(substr($path, 1)));
    foreach ($operation['post']['requestBody']['content']['application/json']['examples'] as $name => $example) {
        $expected = $example['value'];
        $request = new $class();
        $config->projectId = isset($expected['project_id']) ? (int) $expected['project_id'] : null;
        foreach ($expected as $key => $value) {
            if (in_array($key, array('api_version', 'signature', 'project_id'), true)) {
                continue;
            }
            $property = ($path === '/phone_info' && $key === 'user_phone') ? 'phone' : camelCase($key);
            check(property_exists($request, $property), "$class missing $property");
            $request->$property = $value;
        }

        // The API treats optional null request fields as omitted.
        $actual = array_filter($request->getParams($config), function ($value) {
            return $value !== null;
        });
        ksort($actual);
        ksort($expected);
        check('/' . $request->getMethod() === $path, "$class route");
        check($actual === $expected, "$path $name payload or signature");
        $requestCount++;
    }
}

$callback = new MixplatCallback();
$notificationCount = 0;
foreach ($spec['webhooks'] as $hook => $operation) {
    foreach ($operation['post']['requestBody']['content']['application/json']['examples'] as $example) {
        $data = $example['value'];
        $notification = $callback->init(json_encode($data))->getNotify();
        check($notification && $notification->checkSignature($config), "$hook signature");
        foreach ($data as $key => $value) {
            $property = camelCase($key);
            check($notification->$property === $value, "$hook field $key");
        }
        $notificationCount++;
    }
}

foreach (array(false, true) as $allow) {
    ob_start();
    $callback->returnPaymentCheck($allow);
    $decision = json_decode(ob_get_clean(), true);
    check($decision === array('result' => 'ok', 'allow' => (int) $allow), 'payment_check decision');
}

ob_start();
$callback->returnPaymentCheck(true, 1500, 'OK');
$decision = json_decode(ob_get_clean(), true);
check($decision['amount'] === 1500 && $decision['message'] === 'OK', 'payment_check optional fields');

$direct = new \MixplatClient\Method\CreatePayment();
$direct->paymentMethod = 'card';
$direct->redirectUrl = 'https://example.com/return';
$direct->paymentScheme = \MixplatClient\MixplatVars::PAYMENT_SCHEME_DOUBLE;
$params = $direct->getParams($config);
check($params['redirect_url'] === $direct->redirectUrl && $params['payment_scheme'] === 'double', 'card request');

foreach (array('CreatePaymentForm', 'CreatePayment', 'CreateRecurrentPayment') as $method) {
    $class = 'MixplatClient\\Method\\' . $method;
    $request = new $class();
    foreach (array(array('pcs', 'commodity'), array(0, 1)) as $ffd) {
        $request->items = array(array(
            'name' => 'Notebook',
            'quantity' => 3,
            'sum' => 30000,
            'unit' => $ffd[0],
            'payment_object' => $ffd[1],
        ));
        $json = json_decode(json_encode($request->getParams($config)), true);
        check($json['items'] === $request->items, "$method receipt");
    }
}

echo "$requestCount requests and $notificationCount notifications match the OpenAPI examples. Receipt and payment_check tests passed.\n";
