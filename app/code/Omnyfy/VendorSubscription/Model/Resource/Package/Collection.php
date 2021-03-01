<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 20/9/19
 * Time: 1:22 pm
 */
namespace Omnyfy\VendorSubscription\Model\Resource\Package;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Omnyfy\VendorSubscription\Model\Package', 'Omnyfy\VendorSubscription\Model\Resource\Package');
    }
}