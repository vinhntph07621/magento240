<?php

namespace Omnyfy\VendorSignUp\Block\Adminhtml;
use Omnyfy\Vendor\Api\Data\VendorInterface;
use Omnyfy\VendorSignUp\Model\VendorKyc;
use Omnyfy\VendorSignUp\Model\SignUp;

class LoginCheck extends \Magento\Backend\Block\Widget {
    public function __construct(
			\Magento\Backend\Block\Template\Context $context,
            \Magento\Backend\Model\Auth\Session $authSession,
			VendorKyc $vendorKyc, 
			SignUp $signUp,
			array $data = []
        )
    {
        $this->authSession = $authSession;
		$this->_vendorKyc = $vendorKyc;
        $this->_signUp = $signUp;
		parent::__construct($context, $data);
    }
	
	public function getCurrentUser()
	{
		return $this->authSession->getUser();
	}
	
    public function isVendorValidate()
    {
		if($this->getCurrentUser()){
			$role = $this->authSession->getUser()->getRole()->getRoleName();
			
			if (VendorInterface::VENDOR_ADMIN_ROLE === $role) {
				$vendorKycModel = $this->_vendorKyc;
				$userId = $this->getCurrentUser()->getId();
				$vendorKycModel = $vendorKycModel->load($userId, 'vendor_id');
				if($vendorKycModel->getKycStatus()!='approved'){
					return true;
				}
			} 
			return false;
		}	
	}
	
	/**
     * Get URL for approve button
     *
     * @return string
     */
    public function getVendorUrl() {
        return $this->getUrl('omnyfy_vendor/vendor/edit', ['id' => $this->getCurrentUser()->getId()]);
    }
}