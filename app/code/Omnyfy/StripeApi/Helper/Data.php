<?php
/**
 * Project: Strip API
 * User: jing
 * Date: 2019-07-15
 * Time: 15:29
 */
namespace Omnyfy\StripeApi\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    const XML_PATH_KEY = 'omnyfy_stripe/api/key';
    const XML_PATH_DEBUG = 'omnyfy_stripe/api/debug';
    const XML_PATH_PUB_KEY = 'omnyfy_stripe/api/pub_key';
    const XML_PATH_PRO_RATE = 'omnyfy_stripe/api/prorate';

    protected $_key;

    protected $_pubKey;

    protected $_debug;

    protected $_prorate;

    protected $_apiUrl = 'https://api.stripe.com/';

    public function __construct(Context $context)
    {
        parent::__construct($context);
        $this->_key = $this->scopeConfig->getValue(self::XML_PATH_KEY);
        $this->_pubKey = $this->scopeConfig->getValue(self::XML_PATH_PUB_KEY);
        $this->_debug = $this->scopeConfig->getValue(self::XML_PATH_DEBUG);
        $this->_prorate = $this->scopeConfig->getValue(self::XML_PATH_PRO_RATE);
    }

    protected function _sendCurlCmd($url, $method, $headers, $body, $format='json')
    {
        $cmd = 'curl';
        $cmd .= ' -s ';
        $cmd .= ' -X ' . strtoupper($method);
        $cmd .= ' ' . $this->_enc($url);

        $cmd .= ' -H \'Authorization: Bearer ' . $this->_key . '\'';
        foreach($headers as $header => $value) {
            $cmd .= ' -H \'' . $header. ': '.$value.'\'';
        }

        if (!empty($body)) {
            if ('json' == $format) {
                $cmd .= ' -d "' . addslashes(json_encode($body)). '"';
            }
            else {
                foreach($body as $key => $value) {
                    if (is_array($value)) {
                        foreach($value as $v) {
                            $cmd .= ' -d "'. $key.'[]='.$v . '"';
                        }
                    }
                    else {
                        $cmd .= ' -d "'. $key.'='.$value . '"';
                    }
                }

                if ('get'==strtolower($method)) {
                    $cmd .= ' -G';
                }
            }
        }

        $this->_log($cmd, []);

        $result = shell_exec($cmd);

        $this->_log($result, []);

        return $result;
    }

    protected function _log($msg, $data)
    {
        if ($this->_debug) {
            if (is_array($data)) {
                $this->_logger->debug($msg, $data);
            }
            else {
                $this->_logger->debug($msg, [$data]);
            }
        }
    }

    protected function _enc($string)
    {
        $string = str_replace('///', '/', $string);
        $string = str_replace('//', '/', $string);
        $string = str_replace('https:/', 'https://', $string);
        $string = str_replace('http:/', 'http://', $string);
        $string = str_replace(' ', '%20', $string);
        $string = escapeshellarg($string);
        return $string;
    }

    protected function _json($string)
    {
        try{
            $result = json_decode($string, true);
            return $result;
        }
        catch (\Exception $e) {
        }

        return ['errors' => ['Failed to decode response']];
    }

    public function sendRequest($url, $method, $data, $format='form')
    {
        $headers = [];

        if ('json' == $format) {
            $headers['Content-Type'] = 'application/json';
        }
        elseif ('form' == $format) {
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        return $this->_json(
            $this->_sendCurlCmd(
                $this->_apiUrl . '/' . $url,
                $method,
                $headers,
                $data,
                $format
            )
        );
    }

    public function searchCustomer($email)
    {
        return $this->sendRequest(
            '/v1/customers',
            'GET',
            [
                'limit' => 1,
                'email' => $email
            ]
        );
    }

    public function createCustomer($email, $token=null)
    {
        $data = [
            'email' => $email,
            'description' => "Customer for " . $email,
        ];

        if (!empty($token)) {
            $data['source'] = $token;
        }

        return $this->sendRequest(
            '/v1/customers',
            'POST',
            $data
        );
    }

    public function retrieveCustomer($customerId)
    {
        return $this->sendRequest(
            '/v1/customers/' . $customerId,
            'GET',
            []
        );
    }

    public function listProducts($limit)
    {
        $limit = intval($limit);
        $limit = $limit > 0 ? $limit : 10;
        return $this->sendRequest(
            '/v1/products',
            'GET',
            [
                'limit' => $limit
            ]
        );
    }

    public function createProduct($name, $type = 'service')
    {
        return $this->sendRequest(
            '/v1/products',
            'POST',
            [
                'name' => $name,
                'type' => $type
            ]
        );
    }

    public function retrieveProduct($id)
    {
        return $this->sendRequest(
            '/v1/products/' . $id,
            'GET',
            []
        );
    }

    public function createCard($customerId, $token)
    {
        return $this->sendRequest(
            '/v1/customers/'.$customerId . '/sources',
            'POST',
            ['source' => $token]
        );
    }

    public function listCards($customerId, $limit=10)
    {
        $limit = $limit > 0 ? $limit : 10;
        return $this->sendRequest(
            '/v1/customers/' . $customerId . '/sources',
            'GET',
            [
                'object' => 'card',
                'limit' => $limit
            ]
        );
    }

    public function retrieveCard($customerId, $cardId)
    {
        return $this->sendRequest(
            '/v1/customers/' . $customerId . '/sources/' . $cardId,
            'GET',
            []
        );
    }

    public function searchPlan($productId, $limit=10)
    {
        return $this->sendRequest(
            '/v1/plans',
            'GET',
            [
                'product' => $productId,
                'limit' => $limit
            ]
        );
    }

    //For testing only, not for function
    public function createPlan($data)
    {
        $map = [
            'currency' => 'currency',
            'interval' => 'interval',
            'product' => 'product',
            'aggregate_usage' => 'aggregate_usage',
            'amount' => 'amount',
            'billing_scheme' => 'billing_scheme',
            'interval_count' => 'interval_count',
        ];

        $post = [];
        foreach($map as $from => $to) {
            if (isset($data[$from])) {
                $post[$to] = $data[$from];
            }
        }
        return $this->sendRequest(
            '/v1/plans',
            'POST',
            $post
        );
    }

    public function retrievePlan($planId)
    {
        return $this->sendRequest(
            '/v1/plans/' . $planId,
            'GET',
            []
        );
    }

    public function searchSubscription($customerId, $planId)
    {
        return $this->sendRequest(
            '/v1/subscriptions',
            'GET',
            [
                'customer' => $customerId,
                'plan' => $planId
            ]
        );
    }

    public function createSubscription($customerId, $planId, $trialDays=null)
    {
        $data = [
            'customer' => $customerId,
            'items[0][plan]' => $planId
        ];

        if (intval($trialDays) > 0) {
            $data['trial_period_days'] = intval($trialDays);
        }

        return $this->sendRequest(
            '/v1/subscriptions',
            'POST',
            $data
        );
    }

    public function retrieveSubscription($subId)
    {
        return $this->sendRequest(
            '/v1/subscriptions/' . $subId,
            'GET',
            []
        );
    }

    public function cancelSubscription($subId)
    {
        return $this->sendRequest(
            '/v1/subscriptions/' . $subId,
            'POST',
            [
                'cancel_at_period_end' => 'true'
            ]
        );
    }

    public function deleteSubscription($subId)
    {
        return $this->sendRequest(
            '/v1/subscriptions/' . $subId,
            'DELETE',
            []
        );
    }

    public function updateSubscription($subId, $data)
    {
        return $this->sendRequest(
            '/v1/subscriptions/' . $subId,
            'POST',
            $data
        );
    }

    public function listWebhooks($limit=10)
    {
        $limit = $limit < 0 ? 10 : $limit;
        $limit = $limit > 100 ? 100 : $limit;

        return $this->sendRequest(
            '/v1/webhook_endpoints',
            'GET',
            [
                'limit' => $limit
            ]
        );
    }

    public function createWebhook($url, $events)
    {
        $events = is_array($events) ? $events : [$events];
        $data = [
            'url' => $url,
            'enabled_events' => $events
        ];

        return $this->sendRequest(
            '/v1/webhook_endpoints',
            'POST',
            $data
        );
    }

    public function updateWebhook($webHookId, $url, $events)
    {
        $events = is_array($events) ? $events : [$events];
        $data = [
            'url' => $url,
            'enabled_events' => $events
        ];

        return $this->sendRequest(
            '/v1/webhook_endpoints/' . $webHookId,
            'POST',
            $data
        );
    }

    public function getPublicKey()
    {
        return $this->_pubKey;
    }

    public function isProrate()
    {
        return $this->_prorate;
    }
}
 