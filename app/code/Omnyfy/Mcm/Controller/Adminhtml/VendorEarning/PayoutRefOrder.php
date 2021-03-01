<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\VendorEarning;

class PayoutRefOrder extends \Omnyfy\Mcm\Controller\Adminhtml\AbstractAction {

    protected $resourceKey = 'Omnyfy_Mcm::vendor_earning';
    protected $adminTitle = 'Orders Included in Payout Reference';

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_Mcm::vendor_earning');
        $resultPage->addBreadcrumb(__('Vendor Earnings'), __('Orders Included in Payout Reference'));
        $payoutRef = $this->getRequest()->getParam('payout_ref');
        $this->_view->getPage()->getConfig()->getTitle()->prepend($this->adminTitle . ' ' . $payoutRef);
        
        return $resultPage;
    }

}
