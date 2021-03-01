<?php
namespace Omnyfy\VendorSignUp\Controller\Adminhtml\SignUp;

use Omnyfy\Mcm\Model\ResourceModel\VendorPayout;

class Approve extends \Omnyfy\VendorSignUp\Controller\Adminhtml\SignUp
{
    protected $_dataHelper;

    protected $_userFactory;

    protected $_roleCollections;

    protected $_vendorKycFactory;

    protected $_vendorFactory;

    protected $_randomObject;

    protected $_userResourceModel;

    protected $vendorPayoutFactory;

    protected $_vendorPayoutResource;

    protected $_countryFactory;

    protected $_backendHelper;

    protected $_vendorResource;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Omnyfy\VendorSignUp\Model\SignUpFactory $signUpFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory $roleCollections,
        \Omnyfy\VendorSignUp\Helper\Data $dataHelper,
        \Omnyfy\VendorSignUp\Model\VendorKycFactory $vendorKycFactory,
        \Omnyfy\Vendor\Model\VendorFactory $vendorFactory,
        \Magento\Framework\Math\Random $randomObject,
        \Magento\User\Model\ResourceModel\User $userResourceModel,
        \Omnyfy\Mcm\Model\ResourceModel\VendorPayout $vendorPayoutResource,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Omnyfy\VendorSignUp\Helper\Backend $backendHelper,
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource
    ) {
        $this->_userFactory = $userFactory;
        $this->_roleCollections = $roleCollections;
        $this->_dataHelper = $dataHelper;
        $this->_vendorKycFactory = $vendorKycFactory;
        $this->_vendorFactory = $vendorFactory;
        $this->_randomObject = $randomObject;
        $this->_userResourceModel = $userResourceModel;
        $this->_vendorPayoutResource = $vendorPayoutResource;
        $this->_countryFactory = $countryFactory;
        $this->_backendHelper = $backendHelper;
        $this->_vendorResource = $vendorResource;
        parent::__construct($context, $logger, $coreRegistry, $resultPageFactory, $signUpFactory);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
				/* load vendor signup model for update status */
				$model = $this->signUpFactory->create();
                $model->load($id);
				
				if($this->_dataHelper->isAdminAccountExist($model->getEmail())){
					$this->messageManager->addErrorMessage('Vendor account already present in system!');
					$this->_redirect('omnyfy_vendorsignup/signup/listing');
					return;
				}
				
				if($model->getCreatedBy()=='Admin'){
					$this->messageManager->addErrorMessage('We can\'t approve right now.');
					$this->_logger->critical('Created by Admin');
					$this->_redirect('omnyfy_vendorsignup/signup/listing');
					return;
				}
				//TODO: check vendor type exist
                $vendorTypeId = $model->getVendorTypeId();
				$vendorType = $this->_backendHelper->getVendorType($vendorTypeId);
				if (empty($vendorType->getId()) || $vendorTypeId !== $vendorType->getId()) {
				    $this->messageManager->addErrorMessage('Vendor Type "' .$model->getVendorTypeId() . '" not exist');
				    $this->_redirect('omnyfy_vendorsignup/signup/listing');
                }

                //TODO: dispatch a event for validation before create account
                $this->_eventManager->dispatch('omnyfy_vendorsignup_approve_before', ['sign_up' => $model]);

				$businessName = $model->getBusinessName();

				//#[UTH-137] userName should be the same as email, instead of random name
				//$userName = strtolower(substr($string, 0, 5)).$this->_randomObject->getRandomString(5);
                $userName = $model->getEmail();
                //#[UTH-137]
                
				$password = $this->_randomObject->getRandomString(8).'5';

                $roleId = $this->_backendHelper->getRoleId($model);
				
				/* checking role id exist or not */
				if($roleId && $model->getId()){
					/* load user factory for creating user programatically */
					$modelUser = $this->_userFactory->create();        
					$modelUser->setRoleId($roleId);
					$data = array(
						'username' => $userName,
						'firstname' => $model->getFirstName(),
						'lastname' => $model->getLastName(),
						'email' => $model->getEmail(),
						'password' => $password,
						'is_active' => 0,
						'interface_locale' => 'en_US',
						'role_id' => $roleId
					);
										
					$vendorData = array(
                        'name' => $model->getBusinessName(),
                        'address' => $model->getBusinessAddress(),
                        'phone' => $model->getTelephone(),
                        'email' => $model->getEmail(),
                        'description' => $model->getDescription(),
                        'abn' => $model->getAbn(),
                        'status' => \Omnyfy\Vendor\Api\Data\VendorInterface::STATUS_DISABLED,
                        'type_id' => $vendorTypeId,
                        'attribute_set_id' => $vendorType->getVendorAttributeSetId()
						);
					$extraInfo = $model->getExtraInfoAsArray();
                    $extendAttribute = $model->getData('extend_attribute');
                    $extendAttributeArray = json_decode($extendAttribute, true);
                    if(!empty($extendAttributeArray)){
                        foreach ($extendAttributeArray as $code => $value){
                            $vendorData[$code] = $value;
                        }
                    }
                    unset($vendorData['extend_attribute']);
					if (!empty($extraInfo)) {
					    foreach($extraInfo as $key => $value) {
					        $vendorData[$key] = $value;
                        }
                    }
					
					$modelUser->addData($data);
					/* create user with assigned role */
					$this->_userResourceModel->save($modelUser);

					/* add vendor profile from vendor signup */
					$modelVendor = $this->_vendorFactory->create();
					$modelVendor->addData($vendorData);
					$modelVendor->save();

                    $this->_vendorResource->saveUserRelation([
                        'user_id' => $modelUser->getId(),
                        'vendor_id' => $modelVendor->getId()
                    ]);

					if (isset($vendorData['website_ids'])) {
					    $this->_eventManager->dispatch('omnyfy_vendor_update_website_ids',
                            [
                                'website_ids'=> $vendorData['website_ids'],
                                'vendor_id' => $modelVendor->getId()
                            ]
                        );
                    }

                    $model->setData('status', \Omnyfy\VendorSignUp\Model\Source\Status::STATUS_APPROVED);
                    $model->setData('extra_info', json_encode(array_merge($extraInfo, ['password' => $password, 'vendor_id' => $modelVendor->getId()])));
                    $model->save();

                    $kyc = $this->_vendorKycFactory->create();
                    $kyc->setId(null);
                    $kyc->setVendorId($modelVendor->getId());
                    $kyc->setSignupId($model->getId());
                    $kyc->setKycStatus('pending');
                    $kyc->save();

                    $this->_eventManager->dispatch('omnyfy_vendorsignup_approve_after', ['sign_up' => $model, 'vendor' => $modelVendor]);

					//TODO: send email when vendor been activated by subscription callback

                    //Send approval email to vendor with credentials
                    $customerEmail = array(
                        "email" => trim($model->getEmail()),
                        "name" => $businessName
                    );

                    $adminUrl = $this->getAdminUrl();
                    $forgotLink = $this->getAdminForgotPasswordUrl();

                    $vars = [
                        'businessname' => $businessName,
                        'admin_login_link' => $adminUrl,
                        'admin_forgot_password' => $forgotLink
                    ];

                    $this->_dataHelper->sendSignUpApproveToCustomer($vars, $customerEmail);
                    $model->getResource()->updateBindsById(['email_sent' => 1], $model->getId());
				}
				
                $this->messageManager->addSuccessMessage('Congratulations, you have approved '.$businessName.' as a vendor.');
                $this->_redirect('omnyfy_vendorsignup/signup/listing');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_logger->debug($e);
                $this->_redirect('omnyfy_vendorsignup/signup/listing');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage('We can\'t approve right now. Please review the log and try again.');
                $this->_logger->critical($e);
                $this->_redirect('omnyfy_vendorsignup/signup/listing');
                return;
            }
        } else{
			$this->messageManager->addErrorMessage('We can\'t find the sign up request.');
			$this->_redirect('omnyfy_vendorsignup/signup/listing');
		}	
    }

    public function getAdminUrl(){
		return $this->_helper->getHomePageUrl();
    }

    public function getAdminForgotPasswordUrl(){
		return $this->_helper->getHomePageUrl().'auth/forgotpassword/';
    }
	

}