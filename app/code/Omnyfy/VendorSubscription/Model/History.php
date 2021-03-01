<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-01
 * Time: 17:41
 */
namespace Omnyfy\VendorSubscription\Model;

use Omnyfy\VendorSubscription\Api\Data\HistoryInterface;

class History extends \Magento\Framework\Model\AbstractModel implements HistoryInterface 
{
    const CACHE_TAG = 'omnyfy_vendorsubscription_history';

    protected function _construct()
    {
        $this->_init('Omnyfy\VendorSubscription\Model\Resource\History');
    }

    public function getPlanId()
    {
        return $this->getData(self::PLAN_ID);
    }
    
    public function setPlanId($planId)
    {
        return $this->setData(self::PLAN_ID, $planId);
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

    public function getBillingDate()
    {
        return $this->getData(self::BILLING_DATE);
    }

    public function setBillingDate($billingDate)
    {
        return $this->setData(self::BILLING_DATE, $billingDate);
    }

    public function getBillingAccountName()
    {
        return $this->getData(self::BILLING_ACCOUNT_NAME);
    }

    public function setBillingAccountName($billingAccountName)
    {
        return $this->setData(self::BILLING_ACCOUNT_NAME, $billingAccountName);
    }

    public function getPlanPrice()
    {
        return $this->getData(self::PLAN_PRICE);
    }

    public function setPlanPrice($planPrice)
    {
        return $this->setData(self::PLAN_PRICE, $planPrice);
    }

    public function getBillingAmount()
    {
        return $this->getData(self::BILLING_AMOUNT);
    }

    public function setBillingAmount($billingAmount)
    {
        return $this->setData(self::BILLING_AMOUNT, $billingAmount);
    }

    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getInvoiceLink()
    {
        return $this->getData(self::INVOICE_LINK);
    }

    public function setInvoiceLink($invoiceLink)
    {
        return $this->setData(self::INVOICE_LINK, $invoiceLink);
    }
}
 