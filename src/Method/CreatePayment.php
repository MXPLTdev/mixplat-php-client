<?php

namespace MixplatClient\Method;

use MixplatClient\Configuration;

class CreatePayment extends MixplatMethod
{
    /**
     * Уникальный идентификатор запроса, задаваемый ТСП, обеспечивающий идемпотентность вызовов
     * (повторные запросы с тем же request_id не будут приводить к созданию нового платежа,
     * а параметры ответа будут полностью повторять параметры ответа первоначального вызова с данным request_id).
     * Рекомендуется передавать этот параметр, чтобы защититься от дублирования платежей в результате сетевых проблем,
     * задержек ответа и т. п.
     * В качестве request_id можно использовать идентификатор платежа в системе ТСП (если он уникален),
     * или хеш от ключевых параметров запроса.
     * Проверка наличия другого запроса с данным request_id осуществляется за последние 30 дней.
     * От 1 до 64 символов. Необязательный параметр.
     * @var string|null
     */
    public $requestId;

    /**
     * Платёжный метод, который будет использован для совершения оплаты
     * Необязательный, по умолчанию не задан (все доступные методы).
     * \MixplatClient\MixplatVars::PAYMENT_METHOD_*
     * @var string|null
     */
    public $paymentMethod;

    /**
     * ID платежа в ТСП. Если передан, то это же значение параметра будет приходить в уведомлениях payment_status
     * От 1 до 256 символов. Необязательный параметр.
     * @var string|null
     */
    public $merchantPaymentId;

    /**
     * ID кампании в ТСП. Если передан, то это же значение параметра будет приходить в уведомлениях payment_status
     * От 1 до 256 символов. Необязательный параметр.
     * @var string|null
     */
    public $merchantCampaignId;

    /**
     * Произвольные данные ТСП, связанные с платежом.
     * Если передан, то это же значение параметра будет приходить в уведомлениях payment_status
     * От 1 до 256 символов. Необязательный параметр.
     * @var string|null
     */
    public $merchantData;

    /**
     * Массив дополнительных сведений о транзакции, которые ТСП может передать при создании платежа.
     * При уведомлении о статусе оплаты этот массив будет возвращен вместе с остальными параметрами
     * в уведомлении payment_status на Callback URL.
     * Может применяться для передачи сопутствующих данных о плательщике или товаре: по значениям
     * в массиве возможна фильтрация платежей в личном кабинете и выгружаемых XLS отчетах.
     * Необязательный параметр.
     * @var array|null
     */
    public $merchantFields;

    /**
     * Признак тестового платежа.
     * 1: Платёж тестовый
     * 0: Платёж реальный
     * Для тестовых платежей необходимо указывать специальные номера банковских карт/номера телефонов
     * Необязательный параметр, по умолчанию 0 (Платёж реальный).
     * @var int|null
     */
    public $test;

    /**
     * Описание платежа. Отображается в личном кабинете ТСП в информации о платеже и в акцептной смс для payment_method = mobile.
     * От 3 до 64 символов. Необязательный параметр
     * @var string|null
     */
    public $description;

    /**
     * Язык сервисных сообщений
     * Необязательный, по умолчанию "RU".
     * \MixplatClient\MixplatVars::LANGUAGE_*
     * @var string|null
     */
    public $language;

    /**
     * Валюта платежа
     * Необязательный, по умолчанию RUB.
     * \MixplatClient\MixplatVars::CURRENCY_*
     * @var string|null
     */
    public $currency;

    /**
     * Сумма платежа (в минорных единицах, копейках).
     * От 100 до 50000000 для payment_method = card, bank
     * От 1000 до 1500000 для payment_method = mobile, wallet
     * Обязательный параметр
     * @var int
     */
    public $amount;

    /**
     * Данные для чека
     * Необязательный параметр.
     * @var array|null
     */
    public $items;

    /**
     * Email Плательщика. Будет использован для отправки информации о совершённом платеже,
     * если функционал уведомлений активирован для проекта
     * Необязательный параметр.
     * @var string|null
     */
    public $userEmail;

    /**
     * Имя плательщика. Будет использован для отправки информации о совершённом платеже,
     * если функционал уведомлений активирован для проекта
     * Необязательный параметр.
     * @var string|null
     */
    public $userName;

    /**
     * Номер телефона Плательщика в международном формате без символа "+"
     * Обязательный при payment_method = mobile
     * @var string|null
     */
    public $userPhone;

    /**
     * Пользовательский комментарий к платежу (если есть).
     * Необязательный параметр
     * @var string|null
     */
    public $userComment;

    /**
     * Идентификатор пользователя в ТСП (если есть).
     * Необязательный параметр
     * @var string|null
     */
    public $userAccountId;

    /**
     * Время в секундах, отведенное на оплату.
     * После окончания времени на оплату платёж получит статус таймаут и оплата по данному payment_id будет невозможна. Для повторной оплаты необходимо создать новый платёж.
     * Необязательный параметр. По умолчанию 21600 (6 часов).
     * @var int|null
     */
    public $timeout;

    /**
     * JSON-массив с данными банковской карты
     * содержит string-параметры: pan, mm, yy, cvc, name,
     * зашифрованные отдельным ключом шифрования (будет сообщён при интеграции).
     * Обязательный при payment_method = card и возможен только при наличии у ТСП сертификата PCIDSS.
     * @var array|null
     */
    public $userCard;

    /**
     * Для создания подписки передать "recurrent_payment": 1.
     * Подписка создаётся только в случае, если установочный платёж успешен.
     * После проведения установочного платежа вы получите идентификатор подписки recurrent_id в уведомлении payment_status.
     * @var int|null
     */
    public $recurrentPayment;

    /**
     * URL, на который будет перенаправлен Плательщик после успешной оплаты
     * Необязательный параметр. Если не передан, используются настройки платёжной формы, заданные в ЛК.
     * @var string|null
     */
    public $urlSuccess;

    /**
     * URL, на который будет перенаправлен Плательщик после неуспешной оплаты
     * Необязательный параметр. Если не передан, используются настройки платёжной формы, заданные в ЛК.
     * @var string|null
     */
    public $urlFailure;

    /**
     * Схема проведения платежа по банковским картам
     * Необязательный, по умолчанию "sms".
     * \MixplatClient\MixplatVars::CARD_SHEME_*
     * @var string|null
     */
    public $paymentScheme;

    /**
     * UTM метка, определяющая тип трафика. Если параметр передан, то это же значение параметра будет приходить в уведомлениях payment_status
     * Необязательный параметр
     * @var string|null
     */
    public $utmMedium;

    /**
     * UTM метка, определяющая источник трафика. Если параметр передан, то это же значение параметра будет приходить в уведомлениях payment_status
     * Необязательный параметр
     * @var string|null
     */
    public $utmSource;

    /**
     * UTM метка, определяющая рекламную кампанию. Если параметр передан, то это же значение параметра будет приходить в уведомлениях payment_status
     * Необязательный параметр
     * @var string|null
     */
    public $utmCampaign;

    /**
     * Произвольная пользовательская UTM метка. Если параметр передан, то это же значение параметра будет приходить в уведомлениях payment_status
     * Необязательный параметр
     * @var string|null
     */
    public $utmTerm;

    /**
     * @return string
     */
    public function getMethod()
    {
        return 'create_payment';
    }

    /**
     * @param Configuration $config
     * @return array
     */
    public function getParams($config)
    {
        $signature = $this->encryptSignature(
            $this->requestId .
            $config->projectId .
            $this->merchantPaymentId .
            $config->apiKey
        );

        $params = $this->parseParams();
        $params['signature'] = $signature;
        $params['api_version'] = $config->apiVersion;
        $params['project_id'] = $config->projectId;

        return $params;
    }
}
