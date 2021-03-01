<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 20/9/19
 * Time: 11:43 am
 */
namespace Omnyfy\VendorSubscription\Model;

class Package extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Omnyfy\VendorSubscription\Model\Resource\Package');
    }
}
 