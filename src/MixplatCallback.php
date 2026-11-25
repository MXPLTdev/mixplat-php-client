<?php

namespace MixplatClient;

use MixplatClient\Notify\PaymentCheck;
use MixplatClient\Notify\PaymentStatus;
use MixplatClient\Notify\RefundStatus;
use MixplatClient\Notify\SmsNotify;

class MixplatCallback
{
    public $postData;

    /**
     * @param string|null $postdata
     * @return MixplatCallback
     */
    public function init($postdata = null)
    {
        if ($postdata) {
            $this->postData = $postdata;
        } else {
            $this->postData = file_get_contents("php://input");
        }
        return $this;
    }

    /**
     * @return PaymentCheck|PaymentStatus|RefundStatus|SmsNotify|null
     */
    public function getNotify()
    {
        $data = json_decode($this->postData, true);
        if (!isset($data['request']) || !is_string($data['request'])) {
            return null;
        }
        switch ($data['request']) {
            case MixplatVars::NOTIFY_REQUEST_PAYMENT_STATUS:
                $notify = new PaymentStatus();
                return $notify->setParams($data);
                break;

            case MixplatVars::NOTIFY_REQUEST_REFUND_STATUS:
                $notify = new RefundStatus();
                return $notify->setParams($data);
                break;

            case MixplatVars::NOTIFY_REQUEST_PAYMENT_CHECK:
                $notify = new PaymentCheck();
                return $notify->setParams($data);
                break;

            case MixplatVars::NOTIFY_REQUEST_SMS:
                $notify = new SmsNotify();
                return $notify->setParams($data);
                break;

            default:
                return null;
        }
    }

    /**
     * Вернуть "успешно"
     */
    public function returnSuccess($message = null)
    {
        $response = array('result' => MixplatVars::RESULT_OK);
        if ($message !== null) {
            $response['message'] = $message;
        }
        echo json_encode($response);
    }

    /**
     * Ответ на payment_check после проверки заказа продавцом.
     * @param bool|int $allow Разрешить (true, 1) или запретить (false, 0) списание.
     * @param int|null $amount Новая сумма в минорных единицах.
     * @param string|null $message Сообщение плательщику.
     */
    public function returnPaymentCheck($allow, $amount = null, $message = null)
    {
        if (!is_bool($allow) && $allow !== 0 && $allow !== 1) {
            throw new \InvalidArgumentException('allow must be a boolean or the integer 0 or 1.');
        }
        $response = array('result' => MixplatVars::RESULT_OK, 'allow' => (int) $allow);
        if ($amount !== null) {
            $response['amount'] = $amount;
        }
        if ($message !== null) {
            $response['message'] = $message;
        }
        echo json_encode($response);
    }

    /**
     * Вернуть ошибку
     * @param string $errorText
     */
    public function returnError($errorText, $errorDescription = null, $message = null)
    {
        $response = array('result' => $errorText);
        if ($errorDescription !== null) {
            $response['error_description'] = $errorDescription;
        }
        if ($message !== null) {
            $response['message'] = $message;
        }
        echo json_encode($response);
    }

}
