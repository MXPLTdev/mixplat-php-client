<?php
namespace mixplat\mixplatclient;

class Client
{
    /**
     * Api version
     */
    const API_VERSION = 3;

    /**
     * Api host
     */
    const API_HOST = 'https://api.mixplat.com';

    /**
     * Api method for get the payment
     */
    const PAYMENT_METHOD_GET = 'get_payment';

    /**
     * Api method for create a new payment
     */
    const PAYMENT_METHOD_CREATE = 'create_payment';

    /**
     * Api method for create a new payment
     */
    const PAYMENT_METHOD_CREATE_FORM = 'create_payment_form';

    /**
     *
     * Receiving payment information
     *
     * @param array $params
     *
     * @return string
     */
    public function getPayment($params)
    {
        return self::sendHttpRequest($params, self::PAYMENT_METHOD_GET);
    }

    /**
     *
     * Creating a new payment without using a payment form
     *
     * @param array $params
     *
     * @return string
     */
    public function createPayment($params)
    {
        return self::sendHttpRequest($params, self::PAYMENT_METHOD_CREATE);
    }

    /**
     *
     * Creating a new payment using the payment form
     *
     * @param array $params
     *
     * @return string
     */
    public function createPaymentForm($params)
    {
        return self::sendHttpRequest($params, self::PAYMENT_METHOD_CREATE_FORM);
    }

    /**
     *
     * Send http request with post params
     *
     * @param array $params
     * @param string $method
     *
     * @return string
     */
    private static function sendHttpRequest($params, $method)
    {
        $params = array_merge($params, ["api_version" => self::API_VERSION]);

        $context =  stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => json_encode($params)
            ]
        ]);

        return file_get_contents(self::API_HOST . '/' . $method, false, $context);
    }
}