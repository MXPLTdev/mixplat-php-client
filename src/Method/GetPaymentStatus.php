<?php

namespace MixplatClient\Method;

use MixplatClient\Configuration;

class GetPaymentStatus extends MixplatMethod
{
    /**
     * ID платежа в MIXPLAT
     * @var string
     */
    public $paymentId;

    /** @var string|null ID заказа в ТСП для поиска в проекте из Configuration. */
    public $merchantPaymentId;

    /**
     * @return string
     */
    public function getMethod()
    {
        return 'get_payment_status';
    }

    /**
     * @param Configuration $config
     * @return array
     */
    public function getParams($config)
    {
        $signature = $this->encryptSignature(
            $this->paymentId .
            $this->merchantPaymentId .
            $config->apiKey
        );

        $params = $this->parseParams();
        if ($this->merchantPaymentId !== null) {
            if ($this->paymentId !== null || $config->projectId === null) {
                throw new \InvalidArgumentException('Order lookup requires projectId and merchantPaymentId only.');
            }
            unset($params['payment_id']);
            $params['project_id'] = (string) $config->projectId;
        } else {
            unset($params['merchant_payment_id']);
        }
        $params['signature'] = $signature;
        $params['api_version'] = $config->apiVersion;
        return $params;
    }
}
