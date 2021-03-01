<?php
namespace Omnyfy\VendorSignUp\Controller\Adminhtml\SignUp;

/**
 * Sfmc template controller
 */
class Save extends \Omnyfy\VendorSignUp\Controller\Adminhtml\SignUp
{
    protected $_dataHelper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Omnyfy\VendorSignUp\Model\SignUpFactory $signUpFactory,
        \Omnyfy\VendorSignUp\Helper\Data $_dataHelper
    ) {
        $this->_dataHelper = $_dataHelper;
        parent::__construct($context, $logger, $coreRegistry, $resultPageFactory, $signUpFactory);
    }



    public function execute() {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/*'));
        }

        $data = $this->getRequest()->getPostValue();
        $model = $this->signUpFactory->create();

        try {
			if($data['tax_number']=='ABN'){
				if (!$this->_dataHelper->isValidAbn($data['abn'])) {
					throw new \Magento\Framework\Exception\LocalizedException(__('Please enter a valid ABN number.'));
				}
			}
			
            $inputFilter = new \Zend_Filter_Input(
                    [], [], $data
            );
            $data = $inputFilter->getUnescaped();
			
            if (isset($data['id']) && !empty($data['id'])) {
                $id = $data['id'];
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong signup data is specified.'));
                    }
                }
				if ($this->_dataHelper->isAdminAccountExist($data['email']) && $data['email']!=$model->getEmail()) {
					throw new \Magento\Framework\Exception\LocalizedException(
					    __('This email address is already been used. Please use another email or login with your existing vendor account.')
                    );
				}
				$successMessage = 'You have successfully updated "'.$model->getBusinessName().'" signup request.';
            } else {
				$successMessage = 'You saved the signup data.';
                unset($data['id']);
            }

            $this->_eventManager->dispatch('omnyfy_vendor_signup_backend_form_save_before', ['data' => $data, 'sign_up' => $model]);

            $model->addData($data);
            $this->_session->setPageData($data);
            $model->save();

            $this->_eventManager->dispatch('omnyfy_vendor_signup_backend_form_save_after', ['data' => $data, 'sign_up' => $model]);

            $this->messageManager->addSuccessMessage($successMessage);
            $this->_session->setPageData(false);
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', ['id' => $model->getId()]);
                return;
            }
            $this->_redirect('*/*/view', ['id' => $model->getId()]);
            return;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $id = (int) $this->getRequest()->getParam('id');
            if (!empty($id)) {
                $this->_redirect('*/*/edit', ['id' => $id]);
            } 
			//else {
                //$this->_redirect('*/*/new');
           // }
            return;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                'Something went wrong while saving the template data. Please review the error log.'
            );
            $this->_logger->critical($e);
            $this->_session->setPageData($data);
            if (isset($data['id'])) {
                $this->_redirect('*/*/edit', ['id' => $data['id']]);
            } else if ($model->getId()) {
                $this->_redirect('*/*/edit', ['id' => $model->getId()]);
            } else {
                $this->_redirect('*/*/edit', ['id' => '']);
            }

            return;
        }
        $this->_redirect('*/*/');
    }
}