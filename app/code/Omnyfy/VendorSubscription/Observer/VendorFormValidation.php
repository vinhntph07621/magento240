<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-09
 * Time: 16:17
 */
namespace Omnyfy\VendorSubscription\Observer;

use Magento\Framework\Exception\LocalizedException;

class VendorFormValidation implements \Magento\Framework\Event\ObserverInterface
{
    protected $helper;

    public function __construct(
        \Omnyfy\VendorSubscription\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $formData = $observer->getData('form_data');

        if (isset($formData['plan_id']) && isset($formData['subscription_id'])) {
            //validate subscription form data
            $subscription = $this->loadSubscription($formData['subscription_id']);
            if (empty($subscription)) {
                throw new LocalizedException(__('Subscription not exist any more'));
            }

            $plan = $this->loadPlan($formData['plan_id']);
            if (empty($plan)) {
                throw new LocalizedException(__('Plan not exist any more'));
            }

            $planIds = $this->helper->getPlanIdsByVendorTypeId($formData['type_id']);
            if (!in_array($formData['plan_id'], $planIds)) {
                throw new LocalizedException(__('Subscription plan not assigned to this type of vendor yet.'));
            }

            //type_id in form_data should match with subscription's vendor_type_id
            if ($subscription->getVendorTypeId() != $formData['type_id']) {
                throw new LocalizedException(__('Subscription not match with specified vendor type'));
            }
        }
    }

    protected function loadSubscription($subscriptionId)
    {
        return $this->helper->loadSubscriptionById($subscriptionId);
    }

    protected function loadPlan($planId)
    {
        return $this->helper->loadPlanById($planId);
    }
}
 