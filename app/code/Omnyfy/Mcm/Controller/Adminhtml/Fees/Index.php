<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\Fees;

class Index extends \Omnyfy\Mcm\Controller\Adminhtml\AbstractAction {

    protected $resourceKey = 'Omnyfy_Mcm::fees';
    protected $adminTitle = 'Marketplace Fees and Charges Management';

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_Mcm::fees');
        $resultPage->getConfig()->getTitle()->prepend(__('Marketplace Fees and Charges Management'));
        $resultPage->addBreadcrumb(__('Omnyfy'), __('Omnyfy'));
        $resultPage->addBreadcrumb(__('Marketplace Fees and Charges Management'), __('Marketplace Fees and Charges Management'));
        return $resultPage;
    }

}
