<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-01
 * Time: 17:41
 */
namespace Omnyfy\VendorSubscription\Model\Resource;

class History extends \Omnyfy\Core\Model\ResourceModel\AbstractDbModel
{
    protected function _construct()
    {
        $this->_init('omnyfy_vendorsubscription_history', 'id');
    }

    protected function getUpdateFields()
    {
        return [
            'plan_id',
            'vendor_name',
            'billing_date',
            'billing_account_name',
            'plan_price',
            'billing_amount',
            'status'
        ];
    }
}
 