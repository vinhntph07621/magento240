<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-08-07
 * Time: 10:51
 */
namespace Omnyfy\VendorSubscription\Helper;

interface GatewayInterface
{
    public function retrieveCustomer($customerId);

    public function searchCreateCustomer($email, $token);

    public function retrievePlan($planId);

    public function searchCreateSubscription($customerId, $planId, $trailDays=null);

    public function convertStatus($data);

    public function changePlan($subId, $oldPlanId, $newPlanId);
}
 