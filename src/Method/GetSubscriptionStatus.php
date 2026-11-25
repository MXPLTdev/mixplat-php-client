<?php

namespace MixplatClient\Method;

class GetSubscriptionStatus extends MixplatMethod
{
    /** @var int Идентификатор подписки. */
    public $subscriptionId;

    public function getMethod()
    {
        return 'get_subscription_status';
    }

    public function getParams($config)
    {
        $params = $this->parseParams();
        $params['api_version'] = $config->apiVersion;
        $params['signature'] = $this->encryptSignature($this->subscriptionId . $config->apiKey);
        return $params;
    }
}
