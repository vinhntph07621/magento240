<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 12/9/19
 * Time: 2:38 pm
 */
namespace Omnyfy\VendorSubscription\Api\Data;

interface UpdateInterface
{
    const VENDOR_ID = 'vendor_id';

    const SUBSCRIPTION_ID = 'subscription_id';

    const FROM_PLAN_ID = 'from_plan_id';

    const FROM_PLAN_NAME = 'from_plan_name';

    const TO_PLAN_ID = 'to_plan_id';

    const TO_PLAN_NAME = 'to_plan_name';

    const STATUS = 'status';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

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
     * @return int|null
     */
    public function getSubscriptionId();

    /**
     * @param int $subscriptionId
     * @return $this
     */
    public function setSubscriptionId($subscriptionId);

    /**
     * @return int|null
     */
    public function getFromPlanId();

    /**
     * @param int $fromPlanId
     * @return $this
     */
    public function setFromPlanId($fromPlanId);

    /**
     * @return string|null
     */
    public function getFromPlanName();

    /**
     * @param string $fromPlanName
     * @return $this
     */
    public function setFromPlanName($fromPlanName);
    /**
     * @return int|null
     */
    public function getToPlanId();

    /**
     * @param int $toPlanId
     * @return $this
     */
    public function setToPlanId($toPlanId);

    /**
     * @return string|null
     */
    public function getToPlanName();

    /**
     * @param string $toPlanName
     * @return $this
     */
    public function setToPlanName($toPlanName);

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
    public function getCreatedAt();

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);
}
 