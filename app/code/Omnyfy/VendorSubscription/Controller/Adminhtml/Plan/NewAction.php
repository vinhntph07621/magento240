<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-05
 * Time: 12:40
 */
namespace Omnyfy\VendorSubscription\Controller\Adminhtml\Plan;

class NewAction extends \Omnyfy\VendorSubscription\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_VendorSubscription::plans';

    public function execute()
    {
        $this->_forward('edit');
    }
}
 