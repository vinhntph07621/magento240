<?php
namespace Omnyfy\VendorSignUp\Helper;

use \Magento\Framework\App\Area;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MANAGE_SIGNUP_ENABLE = 'omnyfy_vendorsignup/general/enabled';
	
    const SIGNUP_ADMIN_TEMPLATE = 'omnyfy_vendorsignup/vendor_signup/template';
    const SIGNUP_ADMIN_CC = 'omnyfy_vendorsignup/vendor_signup/cc';
	
	const SIGNUP_CUSTOMER_TEMPLATE = 'omnyfy_vendorsignup/vendor_signup_customer/template';
    const SIGNUP_CUSTOMER_CC = 'omnyfy_vendorsignup/vendor_signup_customer/cc';
    const SIGNUP_CUSTOMER_FROM = 'omnyfy_vendorsignup/vendor_signup_customer/sent_from';
	
	const SIGNUP_CUSTOMER_APPROVE_TEMPLATE = 'omnyfy_vendorsignup/vendor_signup_approve/template';
    const SIGNUP_CUSTOMER_APPROVE_CC = 'omnyfy_vendorsignup/vendor_signup_approve/cc';
    const SIGNUP_CUSTOMER_APPROVE_FROM = 'omnyfy_vendorsignup/vendor_signup_approve/sent_from';
	
	const SIGNUP_CUSTOMER_REJECT_TEMPLATE = 'omnyfy_vendorsignup/vendor_signup_reject/template';
    const SIGNUP_CUSTOMER_REJECT_CC = 'omnyfy_vendorsignup/vendor_signup_reject/cc';
    const SIGNUP_CUSTOMER_REJECT_FROM = 'omnyfy_vendorsignup/vendor_signup_reject/sent_from';
	
    const GOOGLE_RECAPTCHA_KEY = 'omnyfy_vendorsignup/google_captcha/site_key';

    const XML_PATH_RETURN_URL = 'omnyfy_vendorsignup/general/return_url';
    const XML_PATH_SUCCESS_URL = 'omnyfy_vendorsignup/general/success_url';

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    private $_inlineTranslation;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $_timezone;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $_date;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $_transportBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @var \Magento\Framework\Url
     */
    private $_url;

    /**
     * @var \Omnyfy\VendorSignUp\Helper\GatewayInterface
     */
    private $_helper;

    protected $_vendorPayoutResource;

    protected $_userFactory;

    protected $_countryFactory;

    protected $_vendorKycFactory;

    protected $_dir;


    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Url $url,
        \Omnyfy\VendorSignUp\Helper\GatewayInterface $helper,
        \Omnyfy\Mcm\Model\ResourceModel\VendorPayout $vendorPayoutResource,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Omnyfy\VendorSignUp\Model\VendorKycFactory $vendorKycFactory,
        \Magento\Framework\Filesystem\DirectoryList $dir
    )
    {
        $this->_date                = $date;
        $this->_timezone            = $timezone;
        $this->_transportBuilder    = $transportBuilder;
        $this->_storeManager        = $storeManager;
        $this->_inlineTranslation   = $inlineTranslation;
        $this->_url                 = $url;
        $this->_helper      = $helper;
        $this->_vendorPayoutResource = $vendorPayoutResource;
        $this->_userFactory = $userFactory;
        $this->_countryFactory = $countryFactory;
        $this->_vendorKycFactory = $vendorKycFactory;
        $this->_dir = $dir;
        parent::__construct($context);
    }

    public function getCurrentDateTime(){
        return $this->_timezone->date(new \DateTime($this->_date->gmtDate()))->format("Y-m-d H:i:s");
    }
	
    public function getFrontendTimeFormat($curTime){
        return $this->_timezone->date(new \DateTime($curTime))->format("D d M, Y");
    }
	
	public function isEnabled(){
        return $this->scopeConfig->getValue(
            self::MANAGE_SIGNUP_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getSignUpAdminTemplate(){
        return $this->scopeConfig->getValue(
            self::SIGNUP_ADMIN_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getSignUpAdminCc(){
        return $this->scopeConfig->getValue(
            self::SIGNUP_ADMIN_CC,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	
	public function getSignUpCustomerTemplate(){
        return $this->scopeConfig->getValue(
            self::SIGNUP_CUSTOMER_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getSignUpCustomerFrom(){
        return $this->scopeConfig->getValue(
            self::SIGNUP_CUSTOMER_FROM,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getSignUpCustomerCc(){
        return $this->scopeConfig->getValue(
            self::SIGNUP_CUSTOMER_CC,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	
	public function getSignUpCustomerApproveTemplate(){
        return $this->scopeConfig->getValue(
            self::SIGNUP_CUSTOMER_APPROVE_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getSignUpCustomerApproveFrom(){
        return $this->scopeConfig->getValue(
            self::SIGNUP_CUSTOMER_APPROVE_FROM,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getSignUpCustomerApproveCc(){
        return $this->scopeConfig->getValue(
            self::SIGNUP_CUSTOMER_APPROVE_CC,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	
	public function getSignUpCustomerRejectTemplate(){
        return $this->scopeConfig->getValue(
            self::SIGNUP_CUSTOMER_REJECT_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getSignUpCustomerRejectFrom(){
        return $this->scopeConfig->getValue(
            self::SIGNUP_CUSTOMER_REJECT_FROM,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getSignUpCustomerRejectCc(){
        return $this->scopeConfig->getValue(
            self::SIGNUP_CUSTOMER_REJECT_CC,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getGoogleReCaptchaKey(){
        return $this->scopeConfig->getValue(
            self::GOOGLE_RECAPTCHA_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	
	public function getStoreName(){
		return $this->scopeConfig->getValue(
			'general/store_information/name',
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}
	
	public function sendSignUpToAdmin($vars){
		$email = $this->scopeConfig->getValue('trans_email/ident_support/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$name  = $this->scopeConfig->getValue('trans_email/ident_support/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$adminEmails = array (
            "email" => $email,
            "name"  => $name
        );

        $from       = "general";
        $templateId = $this->getSignUpAdminTemplate();
        $cc         = $this->getSignUpAdminCc();

        $this->sendEmail($templateId, $vars, $adminEmails, $from, $cc);
    }

    public function sendSignUpToCustomer($vars, $customerEmail) {
        $from       = $this->getSignUpCustomerFrom();

        $templateId = $this->getSignUpCustomerTemplate();
        $cc         = $this->getSignUpCustomerCc();

        $this->sendEmail($templateId, $vars, $customerEmail, $from, $cc);
    }
	
    public function sendSignUpApproveToCustomer($vars, $customerEmail) {
        $from       = $this->getSignUpCustomerApproveFrom();

        $templateId = $this->getSignUpCustomerApproveTemplate();
        $cc         = $this->getSignUpCustomerApproveCc();

        $this->sendEmail($templateId, $vars, $customerEmail, $from, $cc);
    }

    public function sendSignUpRejectToCustomer($vars, $customerEmail) {
        $from       = $this->getSignUpCustomerRejectFrom();

        $templateId = $this->getSignUpCustomerRejectTemplate();
        $cc         = $this->getSignUpCustomerRejectCc();

        $this->sendEmail($templateId, $vars, $customerEmail, $from, $cc);
    }

    public function sendEmail($templateId, $vars, $sendEmail, $from, $cc= null, $area = Area::AREA_FRONTEND) {
        $this->_inlineTranslation->suspend();
        $transport = $this->_transportBuilder
            ->setTemplateIdentifier($templateId)
            ->setTemplateVars($vars)
            ->setTemplateOptions([
                'area' => $area,
                'store' => $this->_storeManager->getStore()->getId()
            ])
            ->setFrom($from)
            ->addTo($sendEmail["email"], $sendEmail["name"]);
		
		if ($cc) {
            $transport = $transport->addCc($cc);
        }

		// Attachment
		if (isset($vars['file'])) {
            $target = $this->_dir->getPath('pub');
		    $file = $target . '/media/omnyfy/vendor_signup/' . date('d-m-Y') . '/' . $vars['file'];
            $transport->addAttachment($file, $vars['file']);
        }
		
        $transport->getTransport()->sendMessage();
        $this->_inlineTranslation->resume();
    }
	
	public function createUser($data){
        return $this->_helper->createUser($data);
	}

    public function updateUser($data, $userId){
        return $this->_helper->updateUser($data, $userId);
    }

	public function createCompany($data){
        return $this->_helper->createCompany($data);
	}
	
	public function updateCompany($data){
        return $this->_helper->updateCompany($data);
	}
	
	public function createBankAccount($data){
        return $this->_helper->createBankAccount($data);
	}

    public function getUserById($userId) {
        return $this->_helper->getUserById($userId);
    }

    public function getBankAccountByUserId($userId) {
        return $this->_helper->getBankAccountByUserId($userId);
    }

    public function getWalletByUserId($userId) {
        return $this->_helper->getWalletByUserId($userId);
    }

    public function readBankAccountId($data) {
        return $this->_helper->readBankAccountId($data);
    }

    public function readUserId($data){
        return $this->_helper->readUserId($data);
    }

    public function readUserStatus($data) {
        return $this->_helper->readUserStatus($data);
    }

    public function readCompanyId($data) {
        return $this->_helper->readCompanyId($data);
    }

    public function readWalletId($data) {
        return $this->_helper->readWalletId($data);
    }

    /**
     * @param $userAccount
     * @return array
     * @deprecated Please use specified method provided by gateway
     */
	public function getAssemblyDetails($userAccount){
	    return ['errors' => 'Deprecated method'];
	}

	public function isUserExist($email) {
	    return $this->_helper->isUserExist($email);
    }

	public function searchUser($email){
	    return $this->_helper->searchUser($email);
	}
	
	public function uuidByEmail($email) {
        $string = md5($email);

        $string = substr($string, 0, 8) . '-' .
            substr($string, 8, 4) . '-' .
            substr($string, 12, 4) . '-' .
            substr($string, 16, 4) . '-' .
            substr($string, 20);

        return $string;
    }
	
	public function getStatusCode($status){
		if($status=='pending'){
			return '23000';
		} else if($status=='pending_check'){
			return '23100';
		} else if($status=='approved_kyc_check'){
			return '23150';
		} else if($status=='approved'){
			return '23200';
		} 
	}

    public function isValidAbn($abn) {
        $weights = array(10, 1, 3, 5, 7, 9, 11, 13, 15, 17, 19);
        // Strip non-numbers from the acn
        $abn = preg_replace('/[^0-9]/', '', $abn);
        // Check abn is 11 chars long
        if (strlen($abn) != 11) {
            return false;
        }
        // Subtract one from first digit
        $abn[0] = ((int) $abn[0] - 1);
        // Sum the products
        $sum = 0;
        foreach (str_split($abn) as $key => $digit) {
            $sum += ($digit * $weights[$key]);
        }
        if (($sum % 89) != 0) {
            return false;
        }
        return true;
    }

    public function isAdminAccountExist($email){
        $user = $this->_userFactory->create()->load($email, 'email');
        if ($user->getId()) {
            return true;
        }

        return false;
    }

    public function updateVendorWallet($payoutData) {
        $this->_vendorPayoutResource->updateWalletInfo(
            $payoutData['vendor_id'],
            $payoutData['ewallet_id'],
            $payoutData['account_ref'],
            $payoutData['third_party_account_id']
        );
    }

    public function kycCheckSignUp($model, $vendorId)
    {
        $country = $this->_countryFactory->create()->loadByCode($model->getCountry());
        $countryCode = $country['iso3_code'];

        $userId = $this->uuidByEmail($model->getEmail());
        $apiData = [
            'id' => $userId,
            'first_name' => $model->getFirstName(),
            'last_name' => $model->getLastName(),
            'email' => $model->getEmail(),
            //'mobile' => $model->getCountryCode().$model->getTelephone(),
            'address_line1' => $model->getBusinessAddress(),
            'state' => $model->getState(),
            'city' => $model->getCity(),
            'zip' => $model->getPostcode(),
            'country' => $model->getCountry(),
            'dob' => date('d/m/Y', strtotime($model->getDob())),
            'tax_number' => $model->getAbn()
        ];

        if($this->isUserExist($model->getEmail())){
            $finalData = $this->updateUser($apiData,$userId);

            if(isset($finalData['errors'])){
                return $finalData;
            } else{
                $vendorKycModel = $this->_vendorKycFactory->create();
                $vendorKycModel->load($model->getId(), 'signup_id');
                $status = $finalData['user']['users']['verification_state'];

                $kycUserId = $userId;
                $companyKycId = $vendorKycModel->getKycCompanyId();
                $walletData = $this->getWalletByUserId($userId);
                $bankData = $this->getBankAccountByUserId($userId);

                /* update company */
                $companyDetails = [
                    "id" => $companyKycId,
                    "user_id" => $kycUserId,
                    "name" => $model->getBusinessName(),
                    "legal_name" => $model->getLegalEntity(),
                    "tax_number" => $model->getAbn(),
                    'address_line1' => $model->getBusinessAddress(),
                    'state' => $model->getState(),
                    'city' => $model->getCity(),
                    'zip' => $model->getPostcode(),
                    'phone' => $model->getCountryCode().$model->getTelephone(),
                    "country" => $countryCode
                ];
                $companyData = $this->updateCompany($companyDetails);
            }
        } else{
            $finalData = $this->createUser($apiData);
            if(isset($finalData['errors'])){
                return $finalData;
            } else{
                $kycUserId = $finalData['users']['id'];
                $status = $finalData['users']['verification_state'];

                /* create company */
                $companyDetails = [
                    "user_id" => $kycUserId,
                    "name" => $model->getBusinessName(),
                    "legal_name" => $model->getLegalEntity(),
                    "tax_number" => $model->getAbn(),
                    'address_line1' => $model->getBusinessAddress(),
                    'state' => $model->getState(),
                    'city' => $model->getCity(),
                    'zip' => $model->getPostcode(),
                    'phone' => $model->getCountryCode().$model->getTelephone(),
                    "country" => $countryCode
                ];
                $companyData = $this->createCompany($companyDetails);

                $companyKycId = $companyData['companies']['id'];

                $walletData = $this->getWalletByUserId($kycUserId);
                $bankData = $this->getBankAccountByUserId($kycUserId);
            }
        }

        $payoutData = [
            'vendor_id' => $vendorId,
            'ewallet_id' => isset($walletData['wallet_accounts']['id']) ? $walletData['wallet_accounts']['id']:null,
            'account_ref' => $kycUserId,
            'third_party_account_id' => isset($bankData['bank_accounts']['id']) ? $walletData['bank_accounts']['id']:null
        ];

        $this->updateVendorWallet($payoutData);
        $statusCode = $this->getStatusCode($status);

        $kycData = [
            'vendor_id' => $vendorId,
            'signup_id' => $model->getId(),
            'kyc_status' => $status,
            'status_code' => $statusCode,
            'kyc_user_id' => $kycUserId,
            'kyc_company_id' => $companyKycId,
            'assembly_response' => '',
        ];

        /* add vendor profile from vendor signup */
        $modelVendorKyc = $this->_vendorKycFactory->create();
        $modelVendorKyc->setData($kycData);
        $modelVendorKyc->save();

        return [
            'status' => $status,
            'userId' => $kycUserId,
            'companyId' => $companyKycId,
            'company' => $companyData,
            'wallet' => $walletData,
            'bank' => $bankData
        ];
    }

    protected function _getConfigedUrl($type) {
        $value = $this->scopeConfig->getValue($type);
        if (empty($value)) {
            return false;
        }

        if (substr($value, 0,1) === '/') {
            $url = substr($value, 1);
            return $this->_getUrl(null, ['_direct' => $url]);
        }
        return $this->_getUrl($value);
    }

    public function getReturnUrl()
    {
        return $this->_getConfigedUrl(self::XML_PATH_RETURN_URL);
    }

    public function getSuccessUrl()
    {
        return $this->_getConfigedUrl(self::XML_PATH_SUCCESS_URL);
    }
}