<?php
namespace Omnyfy\VendorSignUp\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;
use Omnyfy\VendorSignUp\Helper\Data;
use Magento\Framework\Exception\CouldNotSaveException;

class CheckAccount extends \Magento\Framework\App\Action\Action {

    protected $dataPersistor;
    protected $_dataHelper;

    protected $_date;

    protected $_userFactory;

    protected $_storeManager;

    protected $_signUpRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Omnyfy\VendorSignUp\Helper\Data $dataHelper,
        \Magento\Backend\App\Action\Context $context,
        \Omnyfy\VendorSignUp\Model\SignUp $signUpRepository,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->_dataHelper = $dataHelper;
        $this->_date = $date;
        $this->_userFactory = $userFactory;
        $this->_storeManager = $storeManager;
        $this->_signUpRepository = $signUpRepository;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute() {
		
        // Store post data into variable
        $data = $this->getRequest()->getParams();

		try {
			$resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
			/* // initialize current date object
			$curDate = $this->_date->gmtDate(); */
			
			/* $checkEmail = $this->_signUpRepository->load($data['email'],'email');
			if(empty($checkEmail->getData())){ */
			// Create array for save data into table
			if($this->checkAdminAccount($data['email'])){
				return $resultJson->setData([
					"message" => __('This email address is already been used. Please use another email or login with your existing vendor account.'),
					"type" => "exist"
				]);
			} else{
				return $resultJson->setData([
					"message" => __(''),
					"type" => "not"
				]);
			}
		} catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                    'Could not save the deatils:', $exception->getMessage()
            ));
        }
		#$this->_redirect($this->_redirect->getRefererUrl());
    }
	
	public function checkAdminAccount($email){
		$user = $this->_userFactory->create()->load($email, 'email');
		if ($user->getId()) {
			return true;
		} else{
			return false;
		}
	}

}
