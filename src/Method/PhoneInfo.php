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
        return 'phone_info';
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
        unset($params['phone']);
        $params['project_id'] = $config->projectId;
        $params['user_phone'] = $this->phone;
        return $params;
    }
}
