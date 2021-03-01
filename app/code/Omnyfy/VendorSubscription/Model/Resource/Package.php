<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 20/9/19
 * Time: 11:56 am
 */
namespace Omnyfy\VendorSubscription\Model\Resource;

class Package extends \Omnyfy\Core\Model\ResourceModel\AbstractDbModel
{
    protected function _construct()
    {
        $this->_init('omnyfy_vendorsubscription_package', 'package_id');
    }

    protected function getUpdateFields()
    {
        return [
            'name',
            'price',
            'gateway_id'
        ];
    }
}
 