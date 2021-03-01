<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-10
 * Time: 15:34
 */
namespace Omnyfy\VendorSubscription\Observer;

use Magento\Framework\Exception\LocalizedException;

class VendorTypeFormValidation implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $formData = $observer->getData('form_data');

        if ((!isset($formData['role_plan']) || empty($formData['role_plan']))
            && (!isset($formData['vendor_type']['role_plan']) || empty($formData['vendor_type']['role_plan'])) ) {
            //Throw exception

            throw new LocalizedException(__('At least one role and plan combination need be assigned.'));
        }
    }
}
 