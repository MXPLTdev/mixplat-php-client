<?php

namespace MixplatClient\Method;

class GetPaymentStatuses extends MixplatMethod
{
    /** @var string Начало периода по Москве, YYYY-MM-DD HH:MM:SS. */
    public $dateCreatedFrom;

    /** @var string Конец периода в том же формате. */
    public $dateCreatedTo;

    /** @var int|null Номер страницы, начиная с нуля. */
    public $page;

    public function getMethod()
    {
        return 'get_payment_statuses';
    }

    public function getParams($config)
    {
        $params = $this->parseParams();
        $params['api_version'] = $config->apiVersion;
        $params['project_id'] = $config->projectId;
        $params['signature'] = $this->encryptSignature($config->projectId . $config->apiKey);
        return $params;
    }
}
