<?php
namespace Omnyfy\VendorSignUp\Block\Adminhtml\Vendor\Renderer;

use Magento\Framework\DataObject;
use Omnyfy\VendorSignUp\Model\VendorKyc;

class KycInfo extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {

    public function __construct(\Magento\Framework\UrlInterface $urlBuilder, VendorKyc $vendorKyc
    ) {
        $this->_urlBuilder = $urlBuilder;
		$this->vendorKyc = $vendorKyc;
    }

    /**
     * get kyc status
     * @param  DataObject $row
     * @return string
     */
    public function render(DataObject $row) {
		$vendorId = $row->getData('entity_id');
		$vendorKycModel = $this->vendorKyc;
        $vendorKycModel = $vendorKycModel->load($vendorId, 'vendor_id');
		
		return $vendorKycModel->getKycStatus();
	}
}

?>