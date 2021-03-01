<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 12/9/19
 * Time: 2:56 pm
 */
namespace Omnyfy\VendorSubscription\Model;

use Omnyfy\VendorSubscription\Api\Data\UpdateInterface;

class Update extends \Magento\Framework\Model\AbstractModel implements UpdateInterface
{
    protected function _construct()
    {
        $this->_init('Omnyfy\VendorSubscription\Model\Resource\Update');
    }

    public function getVendorId()
    {
        return $this->getData(self::VENDOR_ID);
    }

    public function setVendorId($vendorId)
    {
        return $this->setData(self::VENDOR_ID, $vendorId);
    }

    public function getSubscriptionId()
    {
        return $this->getData(self::SUBSCRIPTION_ID);
    }

    public function setSubscriptionId($subscriptionId)
    {
        return $this->setData(self::SUBSCRIPTION_ID, $subscriptionId);
    }

    public function getFromPlanId()
    {
        return $this->getData(self::FROM_PLAN_ID);
    }

    public function setFromPlanId($fromPlanId)
    {
        return $this->setData(self::FROM_PLAN_ID, $fromPlanId);
    }

    public function getFromPlanName()
    {
        return $this->getData(self::FROM_PLAN_NAME);
    }

    public function setFromPlanName($fromPlanName)
    {
        return $this->setData(self::FROM_PLAN_NAME, $fromPlanName);
    }

    public function getToPlanId()
    {
        return $this->getData(self::TO_PLAN_ID);
    }

    public function setToPlanId($toPlanId)
    {
        return $this->setData(self::TO_PLAN_ID, $toPlanId);
    }

    public function getToPlanName()
    {
        return $this->getData(self::TO_PLAN_NAME);
    }

    public function setToPlanName($toPlanName)
    {
        return $this->setData(self::TO_PLAN_NAME, $toPlanName);
    }

    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}