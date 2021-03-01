<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 20/9/19
 * Time: 1:26 pm
 */
namespace Omnyfy\VendorSubscription\Model\Resource;

class Topup extends \Omnyfy\Core\Model\ResourceModel\AbstractDbModel
{
    protected function _construct()
    {
        $this->_init('omnyfy_vendorsubscription_topup', 'topup_id');
    }

    protected function getUpdateFields()
    {
        return [
            'vendor_id',
            'vendor_name',
            'usage_type_id',
            'package_id',
            'price',
            'limit_count',
            'status'
        ];
    }
}
 