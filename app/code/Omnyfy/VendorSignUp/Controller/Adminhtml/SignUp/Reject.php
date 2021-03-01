<?php

namespace Omnyfy\VendorSignUp\Controller\Adminhtml\SignUp;

class Reject extends \Omnyfy\VendorSignUp\Controller\Adminhtml\SignUp
{
    protected $status = 2;

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

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->signUpFactory->create();
                $model->load($id);
                $model->setData('status', $this->status);
                $model->save();				

				//Send rejection email to vendor with credentials
				$customerEmail = array(
					"email" => trim($model->getEmail()),
					"name" => $model->getBusinessName()
				);
				
				$vars = [
					'businessname' => $model->getBusinessName()
				];
				
				$this->_dataHelper->sendSignUpRejectToCustomer($vars, $customerEmail);
				
                $this->messageManager->addSuccessMessage('You have rejected the vendor signup request from '.$model->getBusinessName());
                $this->_redirect('omnyfy_vendorsignup/signup/listing', ['id' => $model->getData('id')]);
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    'We can\'t reject right now. Please review the log and try again.'
                );
                $this->_logger->critical($e);
                $this->_redirect('omnyfy_vendorsignup/signup/listing', ['id' => $model->getData('id')]);
                return;
            }
        }
        $this->messageManager->addErrorMessage('We can\'t find the sign up request.');
        $this->_redirect('omnyfy_vendorsignup/signup/listing');
    }
}