<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\PendingPayouts;

class Index extends \Omnyfy\Mcm\Controller\Adminhtml\AbstractAction {

    protected $resourceKey = 'Omnyfy_Mcm::pending_payouts';
    protected $adminTitle = 'Pending Payouts';

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_Mcm::pending_payouts');
        $resultPage->getConfig()->getTitle()->prepend(__('Pending Payouts'));
        $resultPage->addBreadcrumb(__('Omnyfy'), __('Omnyfy'));
        $resultPage->addBreadcrumb(__('Pending Payouts'), __('Pending Payouts'));
        return $resultPage;
    }

}
