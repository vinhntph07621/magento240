<?php
/**
 * Project: Strip Subscriptioin
 * User: jing
 * Date: 2019-07-18
 * Time: 17:30
 */
namespace Omnyfy\StripeSubscription\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Omnyfy\VendorSubscription\Model\Source\SubscriptionStatus;

class Data extends AbstractHelper implements \Omnyfy\VendorSubscription\Helper\GatewayInterface
{
    protected $apiHelper;

    protected $contentFactory;

    public function __construct(
        Context $context,
        \Omnyfy\StripeApi\Helper\Data $apiHelper,
        \Omnyfy\StripeSubscription\Model\WebHookContentFactory $contentFactory
    ) {
        parent::__construct($context);
        $this->apiHelper = $apiHelper;
        $this->contentFactory = $contentFactory;
    }

    protected function parseList($data)
    {
        if (isset($data['data'][0]['id'])) {
            return $data['data'];
        }

        return false;
    }

    protected function parseListFirstItem($data)
    {
        if (isset($data['data'][0]['id'])) {
            return $data['data'][0];
        }

        return false;
    }

    /**
     * @param mixed $data
     * @return bool|array
     */
    protected function parseObj($data)
    {
        if (isset($data['id'])) {
            return $data;
        }

        return false;
    }

    public function retrieveCustomer($customerId)
    {
        return $this->parseObj(
            $this->apiHelper->retrieveCustomer($customerId)
        );
    }

    public function searchCustomer($email)
    {
        return $this->parseListFirstItem(
            $this->apiHelper->searchCustomer($email)
        );
    }

    public function createCustomer($email, $token=null)
    {
        return $this->parseObj(
            $this->apiHelper->createCustomer($email, $token)
        );
    }

    public function searchCreateCustomer($email, $token=null)
    {
        $customer = $this->searchCustomer($email);
        if (!empty($customer)) {
            if (isset($customer['sources'])) {
                $source = $this->parseListFirstItem($customer['sources']);
                if (empty($source) && !empty($token)) {
                    $this->createCard($customer['id'], $token);
                }
            }
            return $customer;
        }

        return $this->createCustomer($email, $token);
    }

    public function createCard($customerId, $token)
    {
        return $this->parseObj(
            $this->apiHelper->createCard($customerId, $token)
        );
    }

    public function createSubscription($customerId, $planId, $trialDays=null)
    {
        return $this->parseObj(
            $this->apiHelper->createSubscription($customerId, $planId, $trialDays)
        );
    }

    public function searchSubscription($customerId, $planId)
    {
        return $this->parseListFirstItem(
            $this->apiHelper->searchSubscription($customerId, $planId)
        );
    }

    public function searchCreateSubscription($customerId, $planId, $trialDays=null)
    {
        $subscription = $this->searchSubscription($customerId, $planId);

        if (!empty($subscription)) {
            return $subscription;
        }

        return $this->createSubscription($customerId, $planId, $trialDays);
    }

    public function updateSubscription($subscriptionId, $data)
    {
        return $this->parseObj(
            $this->apiHelper->updateSubscription($subscriptionId, $data)
        );
    }

    public function retrieveSubscription($subscriptionId)
    {
        return $this->parseObj(
            $this->apiHelper->retrieveSubscription($subscriptionId)
        );
    }

    public function changePlan($subId, $oldPlanId, $newPlanId)
    {
        $sub = $this->retrieveSubscription($subId);
        if (empty($sub) || !isset($sub['items']['data'])) {
            return false;
        }

        $itemId = null;
        foreach($sub['items']['data'] as $item) {
            if ($item['plan']['id'] == $oldPlanId) {
                $itemId = $item['id'];
                break;
            }
        }

        if (empty($itemId)) {
            //old plan not match
            return false;
        }

        $data = [
            'items[0][id]'  => $itemId,
            'items[0][plan]' => $newPlanId
        ];
        if ($this->apiHelper->isProrate()) {
            $data['prorate'] = 'true';
        }
        else{
            $data['prorate'] = 'false';
        }
        return $this->updateSubscription($subId, $data);
    }

    public function retrievePlan($planId)
    {
        return $this->parseObj(
            $this->apiHelper->retrievePlan($planId)
        );
    }

    public function convertStatus($data)
    {
        $result = 0;
        switch($data['status']) {
            case 'incomplete':
                $result = SubscriptionStatus::STATUS_PENDING_ACTIVE;
                break;

            case 'trialing':
            case 'active':
                $result = SubscriptionStatus::STATUS_ACTIVE;
                break;

            case 'past_due':
            case 'unpaid':
            case 'incomplete_expired':
                $result = SubscriptionStatus::STATUS_INACTIVE;
                break;
            case 'canceled':
                $result = SubscriptionStatus::STATUS_CANCELLED;
                break;
        }
        return $result;
    }

    public function listWebhooks()
    {
        return $this->parseList(
            $this->apiHelper->listWebhooks()
        );
    }

    public function createWebhook($url, $event)
    {
        return $this->parseObj(
            $this->apiHelper->createWebhook($url, $event)
        );
    }

    public function updateWebhook($webHookId, $url, $event)
    {
        return $this->parseObj(
            $this->apiHelper->updateWebhook($webHookId, $url, $event)
        );
    }

    public function getContentById($contentId)
    {
        if (empty($contentId)) {
            return false;
        }

        $content = $this->contentFactory->create();
        $content->load($contentId);

        if (empty($content->getId()) || $contentId != $content->getId()) {
            return false;
        }

        $str = $content->getContent();
        if (empty($str)) {
            return false;
        }

        $result = false;
        try {
            $result = json_decode($str, true);
        }
        catch(\Exception $e) {
        }

        return $result;
    }
}
 
