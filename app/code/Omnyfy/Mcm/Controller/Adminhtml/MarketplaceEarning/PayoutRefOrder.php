<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\MarketplaceEarning;

class PayoutRefOrder extends \Omnyfy\Mcm\Controller\Adminhtml\AbstractAction {

    protected $resourceKey = 'Omnyfy_Mcm::marketplace_earning';
    protected $adminTitle = 'Paid Outs Included in Payout Reference';

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_Mcm::marketplace_earning');
        $resultPage->addBreadcrumb(__('Marketplace Owner Earnings'), __('Paid Outs Included in Payout Reference'));
        $payoutRef = $this->getRequest()->getParam('payout_ref');
        $this->_view->getPage()->getConfig()->getTitle()->prepend($this->adminTitle . ' ' . $payoutRef);
        
        return $resultPage;
    }

}
