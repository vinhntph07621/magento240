<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 20/9/19
 * Time: 1:25 pm
 */
namespace Omnyfy\VendorSubscription\Model;

class Topup extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Omnyfy\VendorSubscription\Model\Resource\Topup');
    }
}
 