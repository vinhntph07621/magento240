<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-10
 * Time: 15:35
 */
namespace Omnyfy\VendorSubscription\Observer;

use Magento\Framework\Exception\LocalizedException;

class VendorTypeFormSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    protected $planResource;

    public function __construct(
        \Omnyfy\VendorSubscription\Model\Resource\Plan $planResource
    ) {
        $this->planResource = $planResource;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $formData = $observer->getData('form_data');
        $vendorType = $observer->getData('vendor_type');
        $typeId = $vendorType->getId();

        $rolePlans = $formData['role_plan'];
        if (empty($rolePlans)) {
            throw new LocalizedException(__('Role and Plan not assigned'));
        }

        $data = [];
        foreach($rolePlans as $row){
            if (isset($row['delete']) && $row['delete']) {
                continue;
            }

            $config = [];
            foreach($row as $key => $value) {
                if (in_array($key, ['delete', 'role_id', 'plan_id', 'record_id'])) {
                    continue;
                }

                $config[$key] = $value;
            }

            $data[] = [
                'type_id' => $typeId,
                'role_id' => $row['role_id'],
                'plan_id' => $row['plan_id'],
                'config' => $config,
            ];
        }

        $this->planResource->removeAs(['type_id=?' => $typeId], 'omnyfy_vendorsubscription_vendor_type_plan');

        $this->planResource->saveRolePlans($data);
    }
}
 