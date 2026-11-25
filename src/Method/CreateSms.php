<?php

namespace MixplatClient\Method;

class CreateSms extends MixplatMethod
{
    /** @var string|null Идентификатор запроса для повторной отправки без дублирования. */
    public $requestId;

    /** @var int|null Идентификатор входящего SMS при ответе абоненту. */
    public $smsId;

    /** @var string Номер абонента в международном формате без плюса. */
    public $userPhone;

    /** @var string Текст SMS. */
    public $message;

    /** @var string|null Альфа-имя отправителя. */
    public $sender;

    public function getMethod()
    {
        return 'create_sms';
    }

    public function getParams($config)
    {
        $params = $this->parseParams();
        $params['api_version'] = $config->apiVersion;
        $params['project_id'] = $config->projectId;
        $params['signature'] = $this->encryptSignature($this->requestId . $config->projectId . $config->apiKey);
        return $params;
    }
}
