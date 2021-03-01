<?php

namespace Omnyfy\VendorSignUp\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Omnyfy\VendorSignUp\Helper\Data;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Data\Form\FormKey\Validator;

class Save extends \Magento\Framework\App\Action\Action {

    protected $dataPersistor;
    protected $_helper;

    protected $_signUpFactory;

    protected $_userFactory;

    protected $_storeManager;

    protected $resultJsonFactory;

    protected $formKeyValidator;

    protected $_scopeConfig;

    protected $_logger;

    protected $dataMap = [
        'businessname' => 'business_name',
        'firstname' => 'first_name',
        'lastname' => 'last_name',
        'dob' => 'dob',
        'businessaddress' => 'business_address',
        'city' => 'city',
        'state' => 'state',
        'country_id' => 'country',
        'postcode' => 'postcode',
        'countrycode' => 'country_code',
        'contactnumber' => 'telephone',
        'email' => 'email',
        'legal_entity' => 'legal_entity',
        'tax_number' => 'tax_number',
        'businessapn' => 'abn',
        'businessdescription' => 'description',
        'vendor_type_id' => 'vendor_type_id',
    ];

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Omnyfy\VendorSignUp\Helper\Data $helper,
        \Omnyfy\VendorSignUp\Model\SignUpFactory $signUpFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->_helper = $helper;
        $this->_userFactory = $userFactory;
        $this->_storeManager = $storeManager;
        $this->_signUpFactory = $signUpFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_logger = $logger;
        parent::__construct($context);
        $this->formKeyValidator = $context->getFormKeyValidator();
    }

    public function execute()
    {
		 // Store post data into variable
        $data = $this->getRequest()->getParams();
		
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->processResult(null, [
                'error' => true,
                'message' => 'Invalid form key'
            ]);
        }

       

		try {
			if ($this->_helper->isAdminAccountExist($data['email'])) {
			    return $this->processResult(null, [
			        'error' => true,
			        'message' => 'This email address is already been used. Please use another email or login with your existing vendor account.'
                ]);
            }

			//dispatch event before save, chance to validate
			$this->_eventManager->dispatch('omnyfy_vendor_signup_form_save_before', ['data' => $data]);

			unset($data['form_key']);
			unset($data['dobv']);
			//TODO: validate vendor type

			$signup = $this->saveSignup($data);

            //dispatch event after save, to process data for payment gateway
            $this->_eventManager->dispatch('omnyfy_vendor_signup_form_save_after',
                [
                    'data' => $data,
                    'signup' => $signup
                ]
            );

            //Send to Customer
            $customerEmail = array(
                "email" => trim($data['email']),
                "name" => $data['businessname']
            );

            $vars = [
                'businessname' => $data['businessname']
            ];

            if (isset($data['file-name']) && !empty($data['file-name'])) {
                $vars['file'] = $data['file-name'];
            }

            $this->_helper->sendSignUpToCustomer($vars, $customerEmail);
            $this->_helper->sendSignUpToAdmin($vars);

            //RETURN to home page if no success page defined
            $successUrl = $this->_helper->getSuccessUrl();
            $successUrl = empty($successUrl) ? '/' : $successUrl;
            return $this->processResult($successUrl,
                [
                    'success' => true,
                    'message' => 'Thank you for you interest. Our team will contact soon on your request!'
                ]
            );

		} catch (\Exception $exception) {
		    $this->_logger->debug('VENDOR SIGN-UP', $exception->getTrace());
            return $this->processResult(null, [
                'error' => true,
                'message' => __('Could not save the details: %1', $exception->getMessage())
            ]);
        }
    }

    public function saveSignup($data)
    {
        $dataObj = [
            'status' => '0',
            'created_by' => 'Customer'
        ];
        $extendAttr = isset($data['extend_attribute']) ? $data['extend_attribute'] : [];
        if(!empty($extendAttr)){
            $dataObj['extend_attribute'] = json_encode($extendAttr);
        }
        foreach($this->dataMap as $from => $to) {
            $dataObj[$to] = array_key_exists($from, $data) ? $data[$from] : null;
        }

        //attributes in form to save as extended information
        $extra = [];
        foreach($data as $key => $value) {
            if (array_key_exists($key, $this->dataMap)) {
                continue;
            }
            $extra[$key] = $value;
        }

        $extra['website_ids'] = [$this->_storeManager->getWebsite()->getId()];

        $dataObj['extra_info'] = json_encode($extra);


        $signUp = $this->_signUpFactory->create();
        $signUp->addData($dataObj);

        $signUp->save();

        return $signUp;
    }

    public function processResult($backUrl=null, $result=null)
    {
        \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug('***********');
        \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug('***********'.$backUrl);
        \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug('***********',$result);

        $resultJson = $this->resultJsonFactory->create($result);

        if (empty($backUrl)) {
            $backUrl = '/';
        }

        if (!empty($result) && isset($result['message'])) {
            if (isset($result['error'])) {
                $this->messageManager->addErrorMessage($result['message']);
                $resultJson->setData([
                    'success' => $result['error'],
                    'message' => $result['message'],
                    'redirect' => $backUrl
                ]);
            }
            else {
                $this->messageManager->addSuccessMessage($result['message']);
                if (isset($result['success'])) {
                    $resultJson->setData([
                        'success' => $result['success'],
                        'message' => $result['message'],
                        'redirect' => $backUrl
                    ]);
                } else {
                    $this->messageManager->addSuccessMessage($result['message']);
                    $resultJson->setData([
                        'success' => false,
                        'message' => $result['message'],
                        'redirect' => $backUrl
                    ]);
                }

            }
        }


        return $resultJson;

    }

    protected function getBackUrl($defaultUrl = null)
    {
        $returnUrl = $this->getRequest()->getParam('return_url');
        if ($returnUrl && $this->_isInternalUrl($returnUrl)) {
            $this->messageManager->getMessages()->clear();
            return $returnUrl;
        }

        $configUrl = $this->_helper->getReturnUrl();
        if (!empty($configUrl)) {
            return $configUrl;
        }

        if (empty($defaultUrl)) {
            return $this->_url->getUrl('/');
        }

        return $defaultUrl;
    }

    protected function _isInternalUrl($url)
    {
        if (strpos($url, 'http') === false) {
            return false;
        }

        $store = $this->_storeManager->getStore();
        $unsecure = strpos($url, $store->getBaseUrl()) === 0;
        $secure = strpos($url, $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK, true)) === 0;
        return $unsecure || $secure;
    }

}
