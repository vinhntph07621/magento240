<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-01
 * Time: 17:41
 */
namespace Omnyfy\VendorSubscription\Model;

use Omnyfy\VendorSubscription\Api\Data\SubscriptionInterface;

class Subscription extends \Magento\Framework\Model\AbstractModel implements SubscriptionInterface
{
    const CACHE_TAG = 'omnyfy_vendorsubscription_subscription';

    protected $_eventPrefix = 'omnyfy_subscription';

    protected $_eventObject = 'subscription';

    protected function _construct()
    {
        $this->_init('Omnyfy\VendorSubscription\Model\Resource\Subscription');
    }

    public function getVendorId()
    {
        return $this->getData(self::VENDOR_ID);
    }

    public function setVendorId($vendorId)
    {
        return $this->setData(self::VENDOR_ID, $vendorId);
    }

    public function getVendorName()
    {
        return $this->getData(self::VENDOR_NAME);
    }

    public function setVendorName($vendorName)
    {
        return $this->setData(self::VENDOR_NAME, $vendorName);
    }

    public function getPlaneId()
    {
        return $this->getData(self::PLAN_ID);
    }

    public function setPlanId($planId)
    {
        return $this->setData(self::PLAN_ID, $planId);
    }

    public function getPlanName()
    {
        return $this->getData(self::PLAN_NAME);
    }

    public function setPlaneName($planName)
    {
        return $this->setData(self::PLAN_NAME, $planName);
    }

    public function getPlanPrice()
    {
        return $this->getData(self::PLAN_PRICE);
    }

    public function setPlanPrice($planPrice)
    {
        return $this->setData(self::PLAN_PRICE, $planPrice);
    }

    public function getBillingInterval()
    {
        return $this->getData(self::BILLING_INTERVAL);
    }

    public function setBillingInterval($billingInterval)
    {
        return $this->setData(self::BILLING_INTERVAL, $billingInterval);
    }

    public function getTrialDays()
    {
        return $this->getData(self::TRIAL_DAYS);
    }

    public function setTrialDays($trialDays)
    {
        return $this->setData(self::TRIAL_DAYS, $trialDays);
    }

    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getGatewayId()
    {
        return $this->getData(self::GATEWAY_ID);
    }

    public function setGatewayId($gatewayId)
    {
        return $this->setData(self::GATEWAY_ID, $gatewayId);
    }

    public function getVendorTypeId()
    {
        return $this->getData(self::VENDOR_TYPE_ID);
    }

    public function setVendorTypeId($vendorTypeId)
    {
        return $this->setData(self::VENDOR_TYPE_ID, $vendorTypeId);
    }

    public function getRoleId()
    {
        return $this->getData(self::ROLE_ID);
    }

    public function setRoleId($roleId)
    {
        return $this->setData(self::ROLE_ID, $roleId);
    }

    public function getShowOnFront()
    {
        return $this->getData(self::SHOW_ON_FRONT);
    }

    public function setShowOnFront($showOnFront)
    {
        return $this->setData(self::SHOW_ON_FRONT, $showOnFront);
    }

    public function getNextBillingAt()
    {
        return $this->getData(self::NEXT_BILLING_AT);
    }

    public function setNextBillingAt($nextBillingAt)
    {
        return $this->setData(self::NEXT_BILLING_AT, $nextBillingAt);
    }

    public function getCancelledAt()
    {
        return $this->getData(self::CANCELLED_AT);
    }

    public function setCancelledAt($cancelledAt)
    {
        return $this->setData(self::CANCELLED_AT, $cancelledAt);
    }

    public function getExpiryAt()
    {
        return $this->getData(self::EXPIRY_AT);
    }

    public function setExpiryAt($expiryAt)
    {
        return $this->setData(self::EXPIRY_AT, $expiryAt);
    }

    public function getExtraInfo()
    {
        return $this->getData(self::EXTRA_INFO);
    }

    public function setExtraInfo($extraInfo)
    {
        return $this->setData(self::EXTRA_INFO, $extraInfo);
    }

    public function getExtraInfoAsArray()
    {
        $info = $this->getExtraInfo();
        if (empty($info)) {
            return [];
        }

        try{
            $result = json_decode($info, true);
            return $result;
        }
        catch (\Exception $e) {
        }

        return [];
    }
}
 