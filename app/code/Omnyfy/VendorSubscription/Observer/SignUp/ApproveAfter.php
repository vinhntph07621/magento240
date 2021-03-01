<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-08-07
 * Time: 16:22
 */
namespace Omnyfy\VendorSubscription\Observer\SignUp;

use Magento\Framework\Exception\LocalizedException;

class ApproveAfter implements \Magento\Framework\Event\ObserverInterface
{
    protected $helper;

    public function __construct(
        \Omnyfy\VendorSubscription\Helper\Data $helper,
        \Omnyfy\VendorSubscription\Helper\GatewayInterface $gwHelper
    ) {
        $this->helper = $helper;
        $this->gwHelper = $gwHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $vendor = $observer->getData('vendor');
        $signUp = $observer->getData('sign_up');

        $extraInfo = $signUp->getExtraInfoAsArray();

        if (empty($extraInfo) || !is_array($extraInfo) ) {
            throw new LocalizedException(__('Something wrong while trying to approve.'));
        }

        if (!isset($extraInfo['plan_role_id'])) {
            throw new LocalizedException(__('Cannot approve without plan and role selected'));
        }

        list($planId, $roleId) = explode('_', $extraInfo['plan_role_id']);

        $plan = $this->helper->loadPlanById($planId);
        if (empty($plan)) {
            throw new LocalizedException(__('Cannot approve with wrong plan %1', $planId));
        }

        $role = $this->helper->getRoleById($roleId);
        if (empty($roleId) || empty($role->getId())) {
            throw new LocalizedException(__('Cannot approve with wrong role %1', $roleId));
        }

        if (!$plan->getIsFree() && !array_key_exists('card_token', $extraInfo)) {
            throw new LocalizedException(__('Required information for payment not provided'));
        }

        // Check if card_token exists before trying to pass (it won't exist for free plan)
        $cardToken = '';
        if (array_key_exists('card_token', $extraInfo)) {
            $cardToken = $extraInfo['card_token'];
        }

        // create subscription in omnyfy
        $this->helper->createSubscription($signUp, $vendor, $plan, $roleId, $cardToken);

    }
}
 