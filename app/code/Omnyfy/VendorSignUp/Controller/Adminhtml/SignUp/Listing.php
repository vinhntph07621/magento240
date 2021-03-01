<?php
namespace Omnyfy\VendorSignUp\Controller\Adminhtml\SignUp;

class Listing extends \Omnyfy\VendorSignUp\Controller\Adminhtml\SignUp
{
    /**
     * Notifications list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_VendorSignUp::omnyfy_vendorsignup_listing');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Vendor SignUp'));
        $resultPage->addBreadcrumb(__('VendorSignUp'), __('VendorSignUp'));
        $resultPage->addBreadcrumb(__('Manage Vendor SignUp'), __('Manage Vendor SignUp List'));
        return $resultPage;
    }
}