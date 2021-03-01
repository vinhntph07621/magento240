<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-08-14
 * Time: 11:51
 */
namespace Omnyfy\VendorSubscription\Observer\SignUp;

class FormLoadBefore implements \Magento\Framework\Event\ObserverInterface
{
    protected $helper;

    protected $_registry;

    public function __construct(
        \Omnyfy\VendorSubscription\Helper\Data $helper,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->helper = $helper;
        $this->_registry = $coreRegistry;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $planId = $observer->getData('plan_id');
        $vendorType = $observer->getData('vendor_type');

        $plan = $this->helper->loadPlanById($planId);
        if (!empty($plan)) {
            //check relation
            $map = $this->helper->getRoleIdsMapByVendorTypeId($vendorType->getId());
            if (array_key_exists($plan->getId(), $map)) {
                $this->_registry->register('current_omnyfy_plan', $plan);
            }
        }
    }
}
 