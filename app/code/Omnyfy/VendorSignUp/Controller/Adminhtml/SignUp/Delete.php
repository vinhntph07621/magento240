<?php

namespace Omnyfy\VendorSignUp\Controller\Adminhtml\SignUp;

class Delete extends \Omnyfy\VendorSignUp\Controller\Adminhtml\SignUp
{
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->signUpFactory->create();
                $model->load($id);
                $model->delete();				
				
                $this->messageManager->addSuccessMessage('Record deleted successfully!');
                $this->_redirect('omnyfy_vendorsignup/signup/listing');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    'We can\'t delete right now. Please review the log and try again.'
                );
                $this->_logger->critical($e);
                $this->_redirect('omnyfy_vendorsignup/signup/listing');
                return;
            }
        }
        $this->messageManager->addErrorMessage('We can\'t find the delete request.');
        $this->_redirect('omnyfy_vendorsignup/signup/listing');
    }
}