<?php
namespace Omnyfy\VendorSignUp\Cron;
use Omnyfy\VendorSignUp\Model\SignUp;
class KycCheck
{
	protected $_dataHelper;
	protected $_logger;
	protected $_vendorKycFactory;
	protected $_signUpFactory;
	protected $_kycFactory;

	public function __construct(
		\Omnyfy\VendorSignUp\Helper\Data $dataHelper,
		\Magento\Framework\UrlInterface $urlInterface,
		SignUp $signUpFactory,
		\Omnyfy\VendorSignUp\Model\ResourceModel\VendorKyc\CollectionFactory $vendorKycFactory,
		\Omnyfy\VendorSignUp\Model\VendorKycFactory $kycFactory,
		\Omnyfy\Vendor\Model\VendorFactory $vendorFactory,
		\Psr\Log\LoggerInterface $logger
    ) {
		$this->_dataHelper = $dataHelper;
		$this->_urlInterface = $urlInterface;
		$this->_signUpFactory = $signUpFactory;
		$this->_vendorKycFactory = $vendorKycFactory;
		$this->_kycFactory = $kycFactory;
		$this->_vendorFactory = $vendorFactory;
		$this->_logger = $logger;
    }

    public function execute()
    {
		$cron = 'vendor kyc';
		
		$kycCollection = $this->_vendorKycFactory->create()->addAttributeToSelect('*');
		
		$to = date("Y-m-d h:i:s"); // current date
		$from = strtotime('-12 months', strtotime($to));
		$from = date('Y-m-d h:i:s', $from); // 12 months before
		
		$kycCollection->addFieldToFilter('updated_at', array('from'=>$from, 'to'=>$to));
		
		foreach($kycCollection as $kyc):
			$vendor = null;
			$vendorKycModel = null;
			$vendor = $this->_vendorFactory->load($kyc->getVendorId());
			$vendorKycModel = $this->_kycFactory->load($kyc->getId());
			$email = $vendor->getEmail();
			
			$vendorSignup = $this->_signUpFactory->load($email,'email');
			
			$userId = $kyc->getKycUserId();
						
			$data = [
					  'first_name' => $kyc->getFirstname(),
					  'last_name' => $kyc->getLastname(),
					  'email' => $email,
					  'mobile' => $vendor->getPhone(),
					  'address_line1' => $vendorSignup->getBusinessAddress(),
					  'state' => $vendorSignup->getState(),
					  'city' => $vendorSignup->getCity(),
					  'zip' => $vendorSignup->getPostcode(),
					  'country' => $vendorSignup->getCountry(),
					  'dob' => date('d/m/Y', strtotime($kyc->getDob())),
					  'tax_number' => $vendor->getAbn()
					];
			if($userId){
				$apiResult = $this->_dataHelper->getUserById($userId);
				$finalData = json_decode($apiResult,true);
				$status = $finalData['users']['verification_state'];
				$vendorKycModel->setKycStatus($status);
				$vendorKycModel->setStatusCode($this->_dataHelper->getStatusCode($status));
				$vendorKycModel->save();
			}			
		endforeach;
    }
}
