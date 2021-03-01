<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\PayoutHistory;

class Index extends \Omnyfy\Mcm\Controller\Adminhtml\AbstractAction {

    protected $resourceKey = 'Omnyfy_Mcm::payout_history';
    protected $adminTitle = 'Payout History';

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_Mcm::payout_history');
        $resultPage->getConfig()->getTitle()->prepend(__('Payout History'));
        $resultPage->addBreadcrumb(__('Omnyfy'), __('Omnyfy'));
        $resultPage->addBreadcrumb(__('Payout History'), __('Payout History'));
        return $resultPage;
    }

}
