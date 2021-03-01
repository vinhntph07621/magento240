<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\MarketplaceEarning;

class Index extends \Omnyfy\Mcm\Controller\Adminhtml\AbstractAction {

    protected $resourceKey = 'Omnyfy_Mcm::marketplace_earning';
    protected $adminTitle = 'Marketplace Owner Earnings';

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_Mcm::marketplace_earning');
        $this->_view->getPage()->getConfig()->getTitle()->prepend($this->adminTitle);
        $resultPage->addBreadcrumb(__('Marketplace Owner Earnings'), __('Marketplace Owner Earnings'));
        return $resultPage;
    }

}
