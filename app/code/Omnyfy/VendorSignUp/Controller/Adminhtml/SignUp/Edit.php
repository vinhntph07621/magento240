<?php
namespace Omnyfy\VendorSignUp\Controller\Adminhtml\SignUp;

class Edit extends \Omnyfy\VendorSignUp\Controller\Adminhtml\SignUp
{
    /**
     * Templates Edit.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->signUpFactory->create();
        
        if ($id) {
            $model->load($id);
			if ($model->getStatus() != 0) {
				$this->messageManager->addError(__("You can't edit '".$model->getBusinessName()."' signup request."));
				$this->_redirect('*/*/listing');
				return;
			}	
            if (!$model->getId()) {
                $this->messageManager->addError(__('This signup request no longer exists.'));
                $this->_redirect('*/*/listing');
                return;
            }
        }
        
        $this->_coreRegistry->register('current_model', $model);
         
         /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_VendorSignUp::vendorsignup');
        if ($id) {
            $resultPage->getConfig()->getTitle()->prepend(__("Edit Vendor SignUp '%1'", $model->getBusinessName()));
        } /* else {
            $resultPage->getConfig()->getTitle()->prepend(__('Add new Vendor SignUp Template'));
        } */
        $resultPage->addBreadcrumb(__('Vendor SignUp'), __('Vendor SignUp'));
        $resultPage->addBreadcrumb(__('Edit Vendor SignUp'), __('Edit Vendor SignUp View'));
        
        $this->_view->renderLayout();
    }
}