<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-08
 * Time: 18:08
 */
namespace Omnyfy\VendorSubscription\Controller\Adminhtml\History;

class Invoice extends \Omnyfy\VendorSubscription\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_VendorSubscription::histories';

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
 