<?php
/**
 * Project: Vendor SignUp
 * User: jing
 * Date: 6/9/19
 * Time: 11:21 am
 */
namespace Omnyfy\VendorSignUp\Controller\Adminhtml\Kyc;

use Magento\Framework\Exception\LocalizedException;

class Send extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Omnyfy_VendorSignUp::vendorsignup';

    protected $_logger;

    protected $_registry;

    protected $_signUpHelper;

    protected $bankAccountFactory;

    protected $vendorRepository;

    protected $backendHelper;

    protected $payoutResource;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Registry $coreRegistry,
        \Omnyfy\VendorSignUp\Helper\Data $signUpHelper,
        \Omnyfy\Mcm\Model\VendorBankAccountFactory $bankAccountFactory,
        \Omnyfy\Vendor\Api\VendorRepositoryInterface $vendorRepository,
        \Omnyfy\VendorSignUp\Helper\Backend $backendHelper,
        \Omnyfy\Mcm\Model\ResourceModel\VendorPayout $payoutResource
    ) {
        $this->_logger = $logger;
        $this->_registry = $coreRegistry;
        $this->_signUpHelper = $signUpHelper;
        $this->bankAccountFactory = $bankAccountFactory;
        $this->vendorRepository = $vendorRepository;
        $this->backendHelper = $backendHelper;
        $this->payoutResource = $payoutResource;
        parent::__construct($context);
    }

    public function execute()
    {
        $vendorId = $this->getRequest()->getParam('vendor_id');

        $vendor = $this->getVendorById($vendorId);
        if (empty($vendor)) {
            //error
            $this->messageManager->addErrorMessage('Cannot send KYC. Wrong data provided.');
            $this->_redirect('omnyfy_vendor/vendor/index');
            return;
        }

        $kyc = $this->getKycByVendorId($vendorId);

        $bankAccount = $this->bankAccountFactory->create();
        $bankAccount->load($vendorId, 'vendor_id');

        $signUp = $this->getSignUpById($kyc->getData('signup_id'));

        $error = false;
        if (empty($bankAccount->getId())) {
            $this->messageManager->addErrorMessage('Please provide bank details.');
            $error = true;
        }
        elseif (empty($kyc)) {
            $this->messageManager->addErrorMessage('Missing KYC');
            $error = true;
        }
        elseif (empty($signUp)) {
            $this->messageManager->addErrorMessage('Missing sign up data');
            $error = true;
        }

        if ($error) {
            $this->_redirect('omnyfy_vendor/vendor/edit', ['id' => $vendorId]);
            return;
        }

        try{
            $this->processKyc($bankAccount, $signUp, $kyc, $vendor->getEmail());

            $this->messageManager->addSuccessMessage('KYC Processed');

        }
        catch (\Exception $e) {
            $this->messageManager->addErrorMessage('Something wrong with KYC: ' . $e->getMessage());
        }

        $this->_redirect('omnyfy_vendor/vendor/edit', ['id' => $vendorId]);
    }

    protected function getVendorById($vendorId)
    {
        if (empty($vendorId)) {
            return false;
        }

        try {
            $vendor = $this->vendorRepository->getById($vendorId);
            return $vendor;
        }
        catch (\Exception $e)
        {
        }

        return false;
    }

    protected function processKyc($bankAccount, $signUp, $kyc, $email)
    {
        // throw exception if kyc_user_id OR kyc_company_id not set
        if (empty($kyc) || empty($signUp) || empty($bankAccount)) {
            throw new LocalizedException(__('Error in previous KYC flow. Please contact service.'));
        }

        $formData = $bankAccount->getData();
        $formData = array_merge($formData, $signUp->getData());
        $apiData = $this->formatAccountData($formData, $email);

        //search user in gateway by email
        $isEmailExist = $this->_signUpHelper->isUserExist($email);
        if ($isEmailExist) {
            //found user, to update user
            $userData = $this->_signUpHelper->searchUser($email);
            $userId = $this->_signUpHelper->readUserId($userData);

            if (!empty($userId)) {
                $kyc->setKycUserId($userId);
                $userData = $this->_signUpHelper->getUserById($userId);
            }

            //$userData = $this->_signUpHelper->updateUser($apiData, $kyc->getKycUserId());
        }
        else {
            //not found user, to create user
            $userData = $this->_signUpHelper->createUser($apiData);
        }
        $error = $this->loadErrorString($userData);
        if ($error) {
            throw new LocalizedException(__($error));
        }

        $status = $this->_signUpHelper->readUserStatus($userData);
        $status = empty($status) ? \Omnyfy\VendorSignUp\Model\Source\KycStatus::STATUS_PENDING : $status;

        if ($isEmailExist) {
            $kycUserId = $kyc->getKycUserId();

            if (!empty($kyc->getkycCompanyId())) {
                $companyData = $this->_signUpHelper->updateCompany(
                    $this->formatCompanyData($kycUserId, $formData, $kyc->getKycCompanyId())
                );

                $kycCompanyId = $kyc->getKycCompanyId();
            }
            else{
                $companyData = $this->_signUpHelper->createCompany(
                    $this->formatCompanyData($kycUserId, $formData)
                );

                $kycCompanyId = $this->_signUpHelper->readCompanyId($companyData);
            }
        }
        else {
            $kycUserId = $this->_signUpHelper->readUserId($userData);

            $companyData = $this->_signUpHelper->createCompany(
                $this->formatCompanyData($kycUserId, $formData)
            );

            $kycCompanyId = $this->_signUpHelper->readCompanyId($companyData);
        }

        $bankData = $this->_signUpHelper->getBankAccountByUserId($kycUserId);

        $bankAccountId = $this->_signUpHelper->readBankAccountId($bankData);

        if (!$bankAccountId) {
            // create bank account
            $bankData = $this->_signUpHelper->createBankAccount(
                $this->formatBankAccountData($kycUserId, $formData)
            );

            $bankAccountId = $this->_signUpHelper->readBankAccountId($bankData);

            if (!$bankAccountId) {
                throw new LocalizedException(__($this->loadErrorString($bankData)));
            }
        }

        $walletData = $this->_signUpHelper->getWalletByUserId($kycUserId);
        $walletId = $this->_signUpHelper->readWalletId($walletData);

        // Save wallet info
        if (!empty($walletId)) {
            $this->payoutResource->updateWalletInfo($kyc->getVendorId(), $walletId, $kycUserId, $walletId);
        }

        $kyc->setKycStatus($status);
        $kyc->setStatusCode($this->_signUpHelper->getStatusCode($status));
        $kyc->setKycUserId($kycUserId);
        if (!empty($kycCompanyId)) {
            $kyc->setKycCompanyId($kycCompanyId);
        }

        $kyc->save();
    }

    protected function getKycByVendorId($vendorId)
    {
        return $this->backendHelper->getKycByVendorId($vendorId);
    }

    protected function getSignUpById($signUpId)
    {
        return $this->backendHelper->getSignUpById($signUpId);
    }

    protected function formatAccountData($data, $email)
    {
        return [
            'id' => $this->_signUpHelper->uuidByEmail($email),
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
    }

    protected function formatCompanyData($kycUserId, $data, $kycCompanyId=null)
    {
        $result = [
            "user_id" => $kycUserId,
            "name" => $data['business_name'],
            "legal_name" => $data['legal_entity'],
            "tax_number" => $data['abn'],
            'address_line1' => $data['business_address'],
            'state' => $data['state'],
            'city' => $data['city'],
            'zip' => $data['postcode'],
            'phone' => $data['country_code'].$data['telephone'],
            "country" => $this->getCountryISO3Code($data['country'])
        ];
        if (!empty($kycCompanyId)) {
            $result['id'] = $kycCompanyId;
        }
        return $result;
    }

    protected function formatBankAccountData($kycUserId, $data)
    {
        return [
            "user_id" => $kycUserId,
            "bank_name" => $data['bank_name'],
            "account_name" => $data['account_name'],
            "routing_number" => ($data['account_type_id']==1)?$data['bsb']:$data['swift_code'],
            "account_number" => $data['account_number'],
            "account_type" => $data['account_type'],
            "holder_type" => $data['holder_type'],
            "country" => $this->getCountryISO3Code($data['country'])
        ];
    }

    protected function loadErrors($data)
    {
        $result = [];
        if (isset($data['errors'])) {
            if (is_array($data['errors'])) {
                foreach($data['errors'] as $key => $val) {
                    if (is_array($val)) {
                        $result[$key] = $val[0];
                    }
                    else{
                        $result[$key] = $val;
                    }
                }
            }
            else {
                $result['unknown'] = 'Something wrong with gateway';
            }
        }
        return $result;
    }

    protected function loadErrorString($data)
    {
        $errors = $this->loadErrors($data);
        if (empty($errors)) {
            return false;
        }

        $result = '';
        foreach($errors as $key => $val) {
            $result .= 'Invalid user KYC info '. $key. ' '. $val . "\n";
        }
        return $result;
    }

    protected function getCountryISO3Code($code)
    {
        return $this->backendHelper->getCountryISO3Code($code);
    }
}
 