<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-01
 * Time: 17:41
 */
namespace Omnyfy\VendorSubscription\Model;

use Omnyfy\VendorSubscription\Api\Data\PlanInterface;

class Plan extends \Magento\Framework\Model\AbstractModel implements PlanInterface
{
    const CACHE_TAG = 'omnyfy_vendorsubscription_plan';

    protected function _construct()
    {
        $this->_init('Omnyfy\VendorSubscription\Model\Resource\Plan');
    }

    public function getPlanName()
    {
        return $this->getData(self::PLAN_NAME);
    }

    public function setPlanName($planName)
    {
        return $this->setData(self::PLAN_NAME, $planName);
    }

    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    public function getInterval()
    {
        return $this->getData(self::INTERVAL);
    }

    public function setInterval($interval)
    {
        return $this->setData(self::INTERVAL, $interval);
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

    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    public function getBenefits()
    {
        return $this->getData(self::BENEFITS);
    }

    public function getBenefitsArray()
    {
        $str = $this->getBenefits();
        return explode('|', $str);
    }

    public function setBenefits($benefits)
    {
        return $this->setBenefits(self::BENEFITS, $benefits);
    }

    public function getButtonLabel()
    {
        return $this->getData(self::BUTTON_LABEL);
    }

    public function setButtonLabel($buttonLabel)
    {
        return $this->setData(self::BUTTON_LABEL, $buttonLabel);
    }

    public function getPromoText()
    {
        return $this->getData(self::PROMO_TEXT);
    }

    public function setPromoText($promoText)
    {
        return $this->setData(self::PROMO_TEXT, $promoText);
    }

    public function getTrialDays()
    {
        return $this->getData(self::TRIAL_DAYS);
    }

    public function setTrialDays($trialDays)
    {
        return $this->setData(self::TRIAL_DAYS, $trialDays);
    }

    public function getIsFree()
    {
        return $this->getData(self::IS_FREE);
    }

    public function setIsFree($isFree)
    {
        return $this->setData(self::IS_FREE, $isFree);
    }

    public function getShowOnFront()
    {
        return $this->getData(self::SHOW_ON_FRONT);
    }

    public function setShowOnFront($showOnFront)
    {
        return $this->setData(self::SHOW_ON_FRONT, $showOnFront);
    }

    public function getProductLimit()
    {
        return $this->getData(self::PRODUCT_LIMIT);
    }

    public function setProductLimit($productLimit)
    {
        return $this->setData(self::PRODUCT_LIMIT, $productLimit);
    }

    public function getKitStoreLimit()
    {
        return $this->getData(self::KIT_STORE_LIMIT);
    }

    public function setKitStoreLimit($kitStoreLimit)
    {
        return $this->setData(self::KIT_STORE_LIMIT, $kitStoreLimit);
    }

    public function getEnquiryLimit()
    {
        return $this->getData(self::ENQUIRY_LIMIT);
    }

    public function setEnquiryLimit($enquiryLimit)
    {
        return $this->setData(self::ENQUIRY_LIMIT, $enquiryLimit);
    }

    public function getRequestForQuoteLimit()
    {
        return $this->getData(self::REQUEST_FOR_QUOTE_LIMIT);
    }

    public function setRequestForQuoteLimit($requestForQuoteLimit)
    {
        return $this->setData(self::REQUEST_FOR_QUOTE_LIMIT, $requestForQuoteLimit);
    }
}
 