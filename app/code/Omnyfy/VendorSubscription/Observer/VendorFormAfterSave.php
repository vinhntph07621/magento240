<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-09
 * Time: 16:20
 */
namespace Omnyfy\VendorSubscription\Observer;

use Magento\Framework\Exception\LocalizedException;

class VendorFormAfterSave implements \Magento\Framework\Event\ObserverInterface
{
    protected $subToPlanFields = [
        'plan_name' => 'plan_name',
        'plan_price' => 'price',
        'billing_interval' => 'interval',
        'plan_gateway_id' => 'gateway_id',
        'plan_id' => 'plan_id'
    ];

    protected $helper;

    public function __construct(
        \Omnyfy\VendorSubscription\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $vendor = $observer->getData('vendor');
        $formData = $observer->getData('form_data');

        if (!isset($formData['plan_id']) || !isset($formData['subscription_id'])) {
            throw new LocalizedException(__('Plan and Subscription need to be specified.'));
        }

        $plan = $this->helper->loadPlanById($formData['plan_id']);
        if (empty($plan)) {
            throw new LocalizedException(__('Plan does not exist any more'));
        }
        //to load subscription
        $subscription = $this->helper->loadSubscriptionById($formData['subscription_id']);
        if (empty($subscription)) {
            throw new LocalizedException(__('Subscription does not exist any more'));
        }

        $planIds = $this->helper->getPlanIdsByVendorTypeId($vendor->getTypeId());
        if (!in_array($plan->getId(), $planIds)) {
            throw new LocalizedException(__('Plan %1 not been assigned to vendor type %2 yet.', $plan->getPlanName(), $vendor->getTypeId()));
        }

        //set subscription by form_data
        $rolePlans = $this->helper->getRolePlanByVendorTypeId($vendor->getTypeId());
        foreach($rolePlans as $row) {
            if ($plan->getId() == $row['plan_id']) {
                $subscription->setRoleId($row['role_id']);
                break;
            }
        }

        foreach($this->subToPlanFields as $subField => $planField) {
            $subscription->setData($subField, $plan->getData($planField));
        }

        $subscription->setVendorId($vendor->getId());
        $subscription->setVendorName($vendor->getName());

        //save subscription
        $subscription->save();
    }
}
 