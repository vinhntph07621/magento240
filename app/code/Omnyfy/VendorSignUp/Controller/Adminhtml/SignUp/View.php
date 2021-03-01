<?php
namespace Omnyfy\VendorSignUp\Controller\Adminhtml\SignUp;

class View extends \Omnyfy\VendorSignUp\Controller\Adminhtml\SignUp
{
    /**
     * SignUp View.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->signUpFactory->create();
        
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This vendor application no longer exists.'));
                $this->_redirect('*/*');
                return;
            }
        }
        
        $this->_coreRegistry->register('current_model', $model);
         
         /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_VendorSignUp::omnyfy_vendorsignup_signup');
        if ($id) {
            $resultPage->getConfig()->getTitle()->prepend(__("Detail of Vendor Application from '%1'", $model->getBusinessName()));
        } 
		
        $resultPage->addBreadcrumb(__('Vendor SignUp'), __('Vendor SignUp'));
        $resultPage->addBreadcrumb(__('Detail of Vendor Application'), __('Detail of Vendor Application View'));
        
        $this->_view->renderLayout();
    }
}