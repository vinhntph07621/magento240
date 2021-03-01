<?php

namespace Omnyfy\VendorSignUp\Plugin\Vendor\Model;

use Omnyfy\Vendor\Model\Vendor as VendorModel;
use Magento\Store\Model\StoreManagerInterface;
use Omnyfy\VendorSignUp\Model\VendorKyc;
use Omnyfy\VendorSignUp\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Omnyfy\Mcm\Model\VendorPayout;
use Omnyfy\Mcm\Model\VendorBankAccount;

class Vendor {

    protected $newVendor;
    protected $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\RequestInterface $request,
        VendorKyc $vendorKyc,
        ResultFactory $resultFactory,
        RedirectInterface $redirect,
        DataPersistorInterface $dataPersistor,
        UrlInterface $url,
        \Omnyfy\Vendor\Model\VendorFactory $vendorFactory,
        \Omnyfy\VendorSignUp\Model\SignUp $signUpFactory,
        Data $dataHelper,
        VendorPayout $vendorPayoutFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        VendorBankAccount $vendorBankAccount,
        ResponseHttp $response
    ) {
        $this->dataChange = 0;
        $this->newVendor = 0;
        $this->storeManager = $storeManager;
        $this->_logger = $logger;
        $this->messageManager = $messageManager;
        $this->_request = $request;
        $this->vendorKyc = $vendorKyc;
        $this->resultFactory = $resultFactory;
        $this->redirect = $redirect;
        $this->dataPersistor = $dataPersistor;
        $this->_url = $url;
		$this->_vendorFactory = $vendorFactory;
		$this->_signUpFactory = $signUpFactory;
        $this->_dataHelper = $dataHelper;
		$this->vendorPayoutFactory = $vendorPayoutFactory;
		$this->_countryFactory = $countryFactory;
		$this->_vendorBankAccount = $vendorBankAccount;
        $this->response = $response;
    }

    public function beforeSave(VendorModel $subject) {
        if ($subject->isObjectNew()) {
            $this->newVendor = 1;
        }
		$request = $this->_request;
		$id = '';
		$vendorId = '';
		if ($request->isPost() && !$this->newVendor) {
			$data = $request->getPostValue();
			$vendorId = $data['id'];
			unset($data['id']);
			if ($vendorId) {
				$vendor = $this->_vendorFactory->create()->load($vendorId);
				$vendorKyc = $this->vendorKyc->load($vendorId, 'vendor_id');
				$vendorBankAccountModel = $this->_vendorBankAccount->load($vendorId, 'vendor_id');
				$vendorSignUp = $this->_signUpFactory->load($vendorKyc->getSignupId());
			}
			$this->dataChange = 1;
			/* if($data['first_name']!=$vendorSignUp->getFirstName() || $data['last_name']!=$vendorSignUp->getLastName() || $data['kyc_email']!=$vendor->getEmail() || $data['telephone']!=$vendorSignUp->getTelephone() || $data['abn']!=$vendorSignUp->getAbn() || $data['tax_number']!=$vendorSignUp->getTaxNumber() || strtotime($data['dob'])!=strtotime($vendorSignUp->getDob()) || $data['account_name']!=$vendorBankAccountModel->getAccountName() || $data['bank_name']!=$vendorBankAccountModel->getBankName() || $data['bsb']!=$vendorBankAccountModel->getBsb() || $data['account_number']!=$vendorBankAccountModel->getAccountNumber() || $data['account_type']!=$vendorBankAccountModel->getAccountType() || $data['holder_type']!=$vendorBankAccountModel->getHolderType() || $data['acc_country']!=$vendorBankAccountModel->getCountry()){
				$this->dataChange = 1;
			} */
		}
    }

    public function afterSave(VendorModel $subject) {
        try {
            $request = $this->_request;
            $id = '';
            $vendorId = '';
            if ($request->isPost()) {
                $data = $request->getPostValue();
				$this->dataPersistor->set('vendor_bank_acc', $data);
                $vendorKycModel = $this->vendorKyc;
				$country = $this->_countryFactory->create()->loadByCode($data['acc_country']);
				

                    $inputFilter = new \Zend_Filter_Input(
                            [], [], $data
                    );
                    $data = $inputFilter->getUnescaped();
					$email = $data['kyc_email'];
					if($this->dataChange || $this->newVendor){
						if($this->newVendor){
							$dataObj = array
								(
								'business_name' => $data['business_name'],
								'first_name' => $data['first_name'],
								'last_name' => $data['last_name'],
								'dob' => $data['dob'],
								'business_address' => $data['business_address'],
								'city' => $data['city'],
								'state' => $data['state'],
								'country' => $data['country'],
								'postcode' => $data['postcode'],
								'country_code' => $data['country_code'],
								'telephone' => $data['telephone'],
								'email' => $email,
								'legal_entity' => $data['legal_entity'],
								'tax_number' => isset($data['tax_number']) ? $data['tax_number'] : null,
								'abn' => $data['abn'],
								'status' => '0',
								'created_by' => 'Admin',
								'created_at' => ''
							);
							$this->_signUpFactory->setData($dataObj);
							$vendorSignup = $this->_signUpFactory->save();
							
							$kycData = array(
								'vendor_id' => $subject->getId(),
								'signup_id' => $vendorSignup->getId(),
								'kyc_status' => 'pending'
							);
							/* add vendor profile from vendor signup */
							$vendorKycModel->setData($kycData);
							$vendorKycModel->save();
							$vendorKycFactory = $vendorKycModel->load($vendorKycModel->getId());
						} else{					
							$vendorId = $subject->getEntityId();
							if ($vendorId) {
								$vendorKycModel = $vendorKycModel->load($vendorId, 'vendor_id');
								$vendorKycFactory = $vendorKycModel->load($vendorKycModel->getId());
							}
							$vendorSignup = $this->_signUpFactory->load($vendorKycModel->getSignupId());
							$vendorSignup->setBusinessName($data['business_name']);
							$vendorSignup->setFirstName($data['first_name']);
							$vendorSignup->setLastName($data['last_name']);
							$vendorSignup->setDob($data['dob']);
							$vendorSignup->setBusinessAddress($data['business_address']);
							$vendorSignup->setCity($data['city']);
							$vendorSignup->setState($data['state']);
							$vendorSignup->setCountry($data['country']);
							$vendorSignup->setPostcode($data['postcode']);
							$vendorSignup->setCountryCode($data['country_code']);
							$vendorSignup->setTelephone($data['telephone']);
							$vendorSignup->setEmail($data['kyc_email']);
							$vendorSignup->setLegalEntity($data['legal_entity']);
							$vendorSignup->setTaxNumber($data['tax_number']);
							$vendorSignup->setAbn($data['abn']);
							$vendorSignup->save();
							
							if(!$vendorKycModel->getId()){
								$kycData = array(
									'vendor_id' => $subject->getId(),
									'signup_id' => $vendorSignup->getId(),
									'kyc_status' => 'pending'
								);
								/* add vendor profile from vendor signup */
								$vendorKycModel->setData($kycData);
								$vendorKycModel->save();
							}
						}
						$apiData = [
						  'id' => $this->_dataHelper->uuidByEmail($email),
						  'first_name' => $data['first_name'],
						  'last_name' => $data['last_name'],
						  'email' => $email,
						  'mobile' => $data['country_code'].$data['telephone'],
						  'address_line1' => $data['business_address'],
						  'state' => $data['state'],
						  'city' => $data['city'],
						  'zip' => $data['postcode'],
						  'country' => $data['country'],
						  'dob' => date('d/m/Y', strtotime($data['dob'])),
						  'tax_number' => $data['abn']
						];
						$bankAccountId = null;
						if($this->_dataHelper->searchUser($email)){
                            $finalData = $this->_dataHelper->updateUser($apiData,$vendorKycModel->getKycUserId());

							if(isset($finalData['errors'])){
								//$vendorKycFactory->setAssemblyResponse($apiResult);
								//$vendorKycFactory->save();
								foreach($finalData['errors'] as $key => $val){
									$this->messageManager->addError(__('Invaild user kyc information '.$key.' '.$val[0]));
								}
							} else{
								$status = $finalData['users']['verification_state'];
								$bankUrl = $finalData['users']['links']['bank_accounts'];
								$walletUrl = $finalData['users']['links']['wallet_accounts'];
								$kycUserId = $vendorKycModel->getKycUserId();
								$companyKycId = $vendorKycModel->getKycCompanyId();

                                $walletData = $this->_dataHelper->getAssemblyDetails($walletUrl);

                                $bankData = $this->_dataHelper->getAssemblyDetails($bankUrl);
								
								/* update company */ 
								$companyDetails = [
											"id" => $companyKycId,
											"user_id" => $kycUserId,
											"name" => $data['business_name'],
											"legal_name" => $data['legal_entity'],
											"tax_number" => $data['abn'],
											'address_line1' => $data['business_address'],
											'state' => $data['state'],
											'city' => $data['city'],
											'zip' => $data['postcode'],
											'phone' => $data['country_code'].$data['telephone'],
											"country" => $country['iso3_code']
										];
                                $companyData = $this->_dataHelper->updateCompany($companyDetails);
															
								if(isset($bankData['bank_accounts']['id'])){
									$bankAccountId = $bankData['bank_accounts']['id'];
								} else{
									$bankAccount = [
											"user_id" => $kycUserId,
											"bank_name" => $data['bank_name'],
											"account_name" => $data['account_name'],
											"routing_number" => ($data['account_type_id']==1)?$data['bsb']:$data['swift_code'],
											"account_number" => $data['account_number'],
											"account_type" => $data['account_type'],
											"holder_type" => $data['holder_type'],
											"country" => $country['iso3_code']
										];
                                    $bankFinalData = $this->_dataHelper->createBankAccount($bankAccount);

									if(isset($bankFinalData['errors'])){
										 foreach($bankFinalData['errors'] as $key => $val){
											$this->messageManager->addError(__('Please check the bank account information as '.$key.' is '.$val[0]));
										}
									} else{
										$bankAccountId = $bankFinalData['bank_accounts']['id'];
									}	
								}
							}	
						} else{
                            $finalData = $this->_dataHelper->createUser($apiData);

							if(isset($finalData['errors'])){
								//$vendorKycFactory->setAssemblyResponse($apiResult);
								//$vendorKycFactory->save();
								foreach($finalData['errors'] as $key => $val){
									$this->messageManager->addError(__('Invaild user kyc information '.$key.' '.$val[0]));
								}
							} else{							
								$kycUserId = $finalData['users']['id'];
								$status = $finalData['users']['verification_state'];
								
								$bankUrl = $finalData['users']['links']['bank_accounts'];
								$walletUrl = $finalData['users']['links']['wallet_accounts'];

                                $walletData = $this->_dataHelper->getAssemblyDetails($walletUrl);

                                $bankData = $this->_dataHelper->getAssemblyDetails($bankUrl);
								
								/* create company */ 
								$companyDetails = [
											"user_id" => $kycUserId,
											"name" => $data['business_name'],
											"legal_name" => $data['legal_entity'],
											"tax_number" => $data['abn'],
											'address_line1' => $data['business_address'],
											'state' => $data['state'],
											'city' => $data['city'],
											'zip' => $data['postcode'],
											'phone' => $data['country_code'].$data['telephone'],
											"country" => $country['iso3_code']
										];
                                $companyData = $this->_dataHelper->createCompany($companyDetails);
								
								$companyKycId = $companyData['companies']['id'];
								
								if(isset($bankData['bank_accounts']['id'])){
									$bankAccountId = $bankData['bank_accounts']['id'];
								} else{
									$bankAccount = [
											"user_id" => $kycUserId,
											"bank_name" => $data['bank_name'],
											"account_name" => $data['account_name'],
											"routing_number" => ($data['account_type_id']==1)?$data['bsb']:$data['swift_code'],
											"account_number" => $data['account_number'],
											"account_type" => $data['account_type'],
											"holder_type" => $data['holder_type'],
											"country" => $country['iso3_code']
										];
                                    $bankFinalData = $this->_dataHelper->createBankAccount($bankAccount);

									if(isset($bankFinalData['errors'])){
										 foreach($bankFinalData['errors'] as $key => $val){
											$this->messageManager->addError(__('Please check the bank account information as '.$key.' is '.$val[0]));
										}
									} else{
										$bankAccountId = $bankFinalData['bank_accounts']['id'];
									}
								}
							}	
						}
						
						$payoutData = [
										'vendor_id' => $subject->getEntityId(),
										'ewallet_id' => isset($walletData['wallet_accounts']['id']) ? $walletData['wallet_accounts']['id']:null,
										'account_ref' => $kycUserId,
										'third_party_account_id' => $bankAccountId
									];
						
						
						$this->updateVendorWallet($payoutData);
						
						if(!isset($finalData['errors'])){
							$statusCode = $this->_dataHelper->getStatusCode($status);
							$vendorKycFactory->setKycStatus($status);
							$vendorKycFactory->setStatusCode($statusCode);
							$vendorKycFactory->setKycUserId($kycUserId);
							$vendorKycFactory->setKycCompanyId($companyKycId);
							$vendorKycFactory->save();
						}
						//$this->_logger->critical('$KycResult'.$apiResult);
					} 

            }
        } catch (LocalizedException $e) {
            $this->messageManager->addError(
                    __('Something went wrong while saving the kyc details. Please review the error log.')
            );
            $this->_logger->critical($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(
                    __('Something went wrong while saving the kyc details. Please review the error log.')
            );
            $this->_logger->critical($e->getMessage());
        }
    }

    public function redirect($data) {
        $url = $this->_url->getUrl('*/*/edit', ['id' => $data['entity_id'], 'active_tab' => 'vendorsignup_kyc_info']);
        $this->response->setRedirect($url);
        return;
    }
	
	protected function updateVendorWallet($payoutData) {
        //add the failed amount to vendor wallet balance 
        $payoutModel = $this->vendorPayoutFactory->load($payoutData['vendor_id'], 'vendor_id');
        $payoutModel->setEwalletId($payoutData['ewallet_id']);
        $payoutModel->setAccountRef($payoutData['account_ref']);
        $payoutModel->setThirdPartyAccountId($payoutData['third_party_account_id']);
        $payoutModel->save();
    }
}
