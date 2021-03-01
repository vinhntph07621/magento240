<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-08-15
 * Time: 15:30
 */
namespace Omnyfy\VendorSubscription\Model;

class Usage extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Omnyfy\VendorSubscription\Model\Resource\Usage');
    }
}
 