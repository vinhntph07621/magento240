<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-08
 * Time: 17:34
 */
namespace Omnyfy\VendorSubscription\Controller\Adminhtml\Subscription;

class NewAction extends \Omnyfy\VendorSubscription\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_VendorSubscription::subscriptions';

    public function execute()
    {
        $this->_forward('edit');
    }
}
 