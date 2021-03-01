<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-01
 * Time: 17:01
 */
namespace Omnyfy\VendorSubscription\Api\Data;

interface SubscriptionInterface
{
    const VENDOR_ID = 'vendor_id';

    const VENDOR_NAME = 'vendor_name';

    const PLAN_ID = 'plan_id';

    const PLAN_NAME = 'plan_name';

    const PLAN_PRICE = 'plan_price';

    const BILLING_INTERVAL = 'billing_interval';

    const TRIAL_DAYS = 'trial_days';

    const STATUS = 'status';

    const GATEWAY_ID = 'gateway_id';

    const VENDOR_TYPE_ID = 'vendor_type_id';

    const ROLE_ID = 'role_id';

    const SHOW_ON_FRONT = 'show_on_front';

    const NEXT_BILLING_AT = 'next_billing_at';

    const CANCELLED_AT = 'cancelled_at';

    const EXPIRY_AT = 'expiry_at';

    const EXTRA_INFO = 'extra_info';

    /**
     * @return int|null
     */
    public function getVendorId();

    /**
     * @param int $vendorId
     * @return $this
     */
    public function setVendorId($vendorId);

    /**
     * @return string|null
     */
    public function getVendorName();

    /**
     * @param string $vendorName
     * @return $this
     */
    public function setVendorName($vendorName);

    /**
     * @return int|null
     */
    public function getPlaneId();

    /**
     * @param int $planId
     * @return $this
     */
    public function setPlanId($planId);

    /**
     * @return string|null
     */
    public function getPlanName();

    /**
     * @param string $planName
     * @return $this
     */
    public function setPlaneName($planName);

    /**
     * @return float|null
     */
    public function getPlanPrice();

    /**
     * @param float $planPrice
     * @return $this
     */
    public function setPlanPrice($planPrice);

    /**
     * @return int|null
     */
    public function getBillingInterval();

    /**
     * @param int $billingInterval
     * @return $this
     */
    public function setBillingInterval($billingInterval);

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
     * @return int|null
     */
    public function getVendorTypeId();

    /**
     * @param int $vendorTypeId
     * @return $this
     */
    public function setVendorTypeId($vendorTypeId);

    /**
     * @return int|null
     */
    public function getRoleId();

    /**
     * @param int $roleId
     * @return $this
     */
    public function setRoleId($roleId);

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
     * @return string|null
     */
    public function getNextBillingAt();

    /**
     * @param string $nextBillingAt
     * @return $this
     */
    public function setNextBillingAt($nextBillingAt);

    /**
     * @return string|null
     */
    public function getCancelledAt();

    /**
     * @param string $cancelledAt
     * @return $this
     */
    public function setCancelledAt($cancelledAt);

    /**
     * @return string|null
     */
    public function getExpiryAt();

    /**
     * @param string $expiryAt
     * @return $this
     */
    public function setExpiryAt($expiryAt);

    /**
     * @return string|null
     */
    public function getExtraInfo();

    /**
     * @param string $extraInfo
     * @return $this
     */
    public function setExtraInfo($extraInfo);

}
 