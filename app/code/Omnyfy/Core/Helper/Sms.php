<?php

namespace Omnyfy\Core\Helper;

class Sms extends \Magento\Framework\App\Helper\AbstractHelper
{

    const XML_PATH_API_URL = 'omnyfy_core/sms/api_url';
    const XML_PATH_API_USER = 'omnyfy_core/sms/api_user';
    const XML_PATH_API_PASS = 'omnyfy_core/sms/api_pass';
    const XML_PATH_FROM_NUMBER = 'omnyfy_core/sms/from_number';

    const SMS_API_URL = 'https://api.smsglobal.com/http-api.php';
    const SMS_API_USER = 'ov9i7kay';
    const SMS_API_PASS = 'cK9GRFvL';
    //const FROM_NUMBER = '61424458883';
    //No more than 12 char allowed.
    const FROM_NUMBER = 'Omnymart';

    public function send($number, $message)
    {
        $data = [
            'action' => 'sendsms',
            'user' => $this->getApiUser(),
            'password' => $this->getApiPass(),
            'from' => $this->getFromNumber(),
            'to' => $number,
            'text' => urlencode($message)
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getApiUrl() . '?' . http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        curl_close($ch);

        return $this;
    }

    protected function getApiUrl()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_API_URL);
    }

    protected function getApiUser()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_API_USER);
    }

    protected function getApiPass()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_API_PASS);
    }

    protected function getFromNumber()
    {
        $result = $this->scopeConfig->getValue(self::XML_PATH_FROM_NUMBER);
        $result = empty($result)? self::FROM_NUMBER : $result;
        $result = strlen($result) > 12 ? substr($result, 0, 12) : $result;
        return $result;
    }
}
