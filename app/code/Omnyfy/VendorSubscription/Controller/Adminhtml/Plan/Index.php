<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-02
 * Time: 14:24
 */
namespace Omnyfy\VendorSubscription\Controller\Adminhtml\Plan;

class Index extends \Omnyfy\VendorSubscription\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_VendorSubscription::plans';

    protected $adminTitle = 'Subscription Plans';

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Subscription Plan'));
        return $resultPage;
    }
}
 