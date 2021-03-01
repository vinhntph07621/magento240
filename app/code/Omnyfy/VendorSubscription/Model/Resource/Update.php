<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 12/9/19
 * Time: 3:05 pm
 */
namespace Omnyfy\VendorSubscription\Model\Resource;

class Update extends \Omnyfy\Core\Model\ResourceModel\AbstractDbModel
{
    protected function _construct()
    {
        $this->_init('omnyfy_vendorsubscription_update', 'update_id');
    }

    protected function getUpdateFields()
    {
        return [
            'vendor_id',
            'subscription_id',
            'from_plan_id',
            'from_plan_name',
            'to_plan_id',
            'to_plan_name',
            'status'
        ];
    }
}
 