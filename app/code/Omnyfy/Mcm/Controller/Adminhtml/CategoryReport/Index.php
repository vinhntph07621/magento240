<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\CategoryReport;

class Index extends \Omnyfy\Mcm\Controller\Adminhtml\AbstractAction {

    protected $resourceKey = 'Omnyfy_Mcm::category_commission_reports';
    protected $adminTitle = 'Category Commission Report';

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_Mcm::category_commission_reports');
        $resultPage->getConfig()->getTitle()->prepend(__('Category Commission Report'));
        $resultPage->addBreadcrumb(__('Omnyfy Commission Report'), __('Omnyfy Commission Report'));
        $resultPage->addBreadcrumb(__('Category Commission Report'), __('Category Commission Report'));
        return $resultPage;
    }

}
