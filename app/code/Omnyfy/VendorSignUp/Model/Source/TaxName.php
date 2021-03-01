<?php

namespace Omnyfy\VendorSignUp\Model\Source;

class TaxName implements \Magento\Framework\Option\ArrayInterface {
	
	protected $request;
    public function __construct(
        \Magento\Framework\App\Request\Http $request
    ) {
       $this->request = $request;
    }
    public function getIddata()
    {
		$this->request->getParams(); 
        return $this->request->getParam('id');
    }
	
    /**
     * Retrieve Vendor bank account type options array.
     *
     * @return array
     */
    public function toOptionArray() {
		return $this->_toArray();
	}
	
	
    public function _toArray() {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$signUpData = $objectManager->create('Omnyfy\VendorSignUp\Model\SignUp')->load($this->getIddata());
		$taxName = $signUpData->getTaxNumber();
		$countryId = $signUpData->getCountry();
		if($countryId=='US'){
			return [
				['value' => 'EIN', 'label' => __('EIN')]
			];
		} else if($countryId=='AU'){ 
			return [
				['value' => 'ABN', 'label' => __('ABN')],
				['value' => 'ACN', 'label' => __('ACN')]
			];
		} else if($countryId=='NZ'){ 
			return [
				['value' => 'NZBN', 'label' => __('NZBN')],
				['value' => 'NZCN', 'label' => __('NZCN')]
			];
		} else if($countryId=='ZA'){ 
			return [
				['value' => 'CIPC', 'label' => __('CIPC')],
				['value' => 'SARSNZ', 'label' => __('SARSNZ')]
			];
		} else{
			return [
				['value' => 'NA', 'label' => __('N/A')]
			];
		}
		
		return true;
    }
}
