<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\VendorWithdrawal;

class View extends \Omnyfy\Mcm\Controller\Adminhtml\AbstractAction {

    protected $resourceKey = 'Omnyfy_Mcm::vendor_withdrawal';
    protected $adminTitle = 'Withdrawal Details';

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $id = $this->getRequest()->getParam('id');
        
        if ($id) {
            $resultPage->getConfig()->getTitle()->prepend(__('Withdrawal Details for ' . $id));
        } else {
            $this->messageManager->addError(
                    __("This withdrawal doesn't exist in Marketplace Commercials Management.")
            );
            $resultPage->getConfig()->getTitle()->prepend(__($this->adminTitle));
        }

        $resultPage->addBreadcrumb(__('Withdrawal History'), __($this->adminTitle));

        return $resultPage;
    }

}
