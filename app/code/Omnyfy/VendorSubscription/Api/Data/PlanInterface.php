<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-01
 * Time: 17:00
 */
namespace Omnyfy\VendorSubscription\Api\Data;

interface PlanInterface
{
    const PLAN_NAME = 'plan_name';

    const PRICE = 'price';

    const INTERVAL = 'interval';

    const STATUS = 'status';

    const GATEWAY_ID = 'gateway_id';

    const SHOW_ON_FRONT = 'show_on_front';

    const DESCRIPTION = 'description';

    const BENEFITS = 'benefits';

    const BUTTON_LABEL = 'button_label';

    const PROMO_TEXT = 'promo_text';

    const TRIAL_DAYS = 'trial_days';

    const IS_FREE = 'is_free';

    const PRODUCT_LIMIT = 'product_limit';

    const KIT_STORE_LIMIT = 'kit_store_limit';

    const ENQUIRY_LIMIT = 'enquiry_limit';

    const REQUEST_FOR_QUOTE_LIMIT = 'request_for_quote_limit';

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return string|null
     */
    public function getPlanName();

    /**
     * @param string $planName
     * @return $this
     */
    public function setPlanName($planName);

    /**
     * @return float|null
     */
    public function getPrice();

    /**
     * @param float $price
     * @return $this
     */
    public function setPrice($price);

    /**
     * @return int|null
     */
    public function getInterval();

    /**
     * @param int $interval
     * @return $this
     */
    public function setInterval($interval);

    /**
     * @return int|null
     */
    public function getStatus();

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * @return string|null
     */
    public function getGatewayId();

    /**
     * @param string $gatewayId
     * @return $this
     */
    public function setGatewayId($gatewayId);

    /**
     * @return string|null
     */
    public function getDescription();

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * @return string|null
     */
    public function getBenefits();

    /**
     * @param string $benefits
     * @return $this
     */
    public function setBenefits($benefits);

    /**
     * @return string|null
     */
    public function getButtonLabel();

    /**
     * @param string $buttonLabel
     * @return $this
     */
    public function setButtonLabel($buttonLabel);

    /**
     * @return string|null
     */
    public function getPromoText();

    /**
     * @param string $promoText
     * @return $this
     */
    public function setPromoText($promoText);

    /**
     * @return int|null
     */
    public function getTrialDays();

    /**
     * @param int $trialDays
     * @return $this
     */
    public function setTrialDays($trialDays);

    /**
     * @return int|null
     */
    public function getIsFree();

    /**
     * @param int $isFree
     * @return $this
     */
    public function setIsFree($isFree);

    /**
     * @return int|null
     */
    public function getShowOnFront();

    /**
     * @param int $showOnFront
     * @return $this
     */
    public function setShowOnFront($showOnFront);

    /**
     * @return int|null
     */
    public function getProductLimit();

    /**
     * @param int $productLimit
     * @return $this
     */
    public function setProductLimit($productLimit);

    /**
     * @return int|null
     */
    public function getKitStoreLimit();

    /**
     * @param int $kitStoreLimit
     * @return $this
     */
    public function setKitStoreLimit($kitStoreLimit);

    /**
     * @return int|null
     */
    public function getEnquiryLimit();

    /**
     * @param int $enquiryLimit
     * @return $this
     */
    public function setEnquiryLimit($enquiryLimit);

    /**
     * @return int|null
     */
    public function getRequestForQuoteLimit();

    /**
     * @param int $requestForQuoteLimit
     * @return $this
     */
    public function setRequestForQuoteLimit($requestForQuoteLimit);
}
 