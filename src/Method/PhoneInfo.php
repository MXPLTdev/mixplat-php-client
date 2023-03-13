<?php

namespace MixplatClient\Method;

use MixplatClient\Configuration;

class PhoneInfo extends MixplatMethod
{
    /**
     * Номер телефона в международном формате без символа "+".
     * @var string
     */
    public $phone;


    /**
     * @return string
     */
    public function getMethod()
    {
        return 'mc/phone_information';
    }

    /**
     * @param Configuration $config
     * @return array
     */
    public function getParams($config)
    {
        $signature = $this->encryptSignature(
            $config->projectId .
            $this->phone .
            $config->apiKey
        );

        $params = $this->parseParams();
        $params['signature'] = $signature;
        $params['api_version'] = $config->apiVersion;
        $params['service_id'] = $config->projectId;
        return $params;
    }
}
