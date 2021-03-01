<?php


namespace Omnyfy\VendorSearch\Block\Search;

class Form extends \Magento\Framework\View\Element\Template
{
    /** @var \Omnyfy\Vendor\Api\VendorAttributeRepositoryInterface $_vendorMetadataService */
    protected $_vendorMetadataService;

    /** @var \Magento\Framework\Api\SearchCriteriaBuilder $_searchCriteriaBuilder */
    protected $_searchCriteriaBuilder;

    /** @var \Omnyfy\VendorSearch\Helper\Data $_data */
    protected $_helperData;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Omnyfy\Vendor\Api\VendorAttributeRepositoryInterface $vendorMetadataService,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Omnyfy\VendorSearch\Helper\Data $helperData,
        array $data = []
    ){
        $this->_vendorMetadataService = $vendorMetadataService;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_helperData = $helperData;
        parent::__construct($context, $data);
    }

    public function getNumActiveForms(){
        return $this->getData('num_active_forms');
    }

    public function getSearchForms(){
        $forms = $this->getData('forms');
        $formsArray = [];

        foreach($forms as $form){
            if (array_key_exists("websites", $form) && is_array($form["websites"])){
                if (array_key_exists($this->getStoreId(),$form["websites"])){
                    $formsArray[] = $form;
                }
            }
        }

        return $formsArray;
    }

    public function isFormActive($vendorType, $currentFormId, $isFirstForm){

        if ($vendorType == "" && $isFirstForm)
            return "active";


        if ($vendorType == $currentFormId)
            return "active";

        return "";
    }

    public function isOptionActive($currentValue, $optionValue){
        if ($currentValue == $optionValue)
            return "selected";
        return "";
    }

    public function getTypesDropDown(){
        return $this->getChildHtml('vendor.search.form.types.container');
    }

    public function getFieldOptions($field){

        try {
            $attributeValues = [];

            if (key_exists('attribute_code', $field)) {
                /** @var \Omnyfy\Vendor\Api\Data\VendorAttributeInterface $attribute */
                $attribute = $this->_vendorMetadataService->get($field['attribute_code']);

                $options = $attribute->getOptions();
                $attributeValues = [];

                foreach ($options as $option) {
                    $attributeValues[$option->getValue()] = $option->getLabel();
                }
            }

            return $attributeValues;

        } catch (\Exception $exception){
            $this->_logger->debug($exception->getMessage());
            return [];
        }
    }

    public function getSearchPostUrl($uri,$param = null)
    {
        return $this->getUrl($uri, $param);
    }

    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @return mixed
     */
    public function isDisplayForm(){
        return $this->_helperData->isSearchForm();
    }

    /**
     * @return mixed
     */
    public function isDistance(){
        return $this->_helperData->isFilters();
    }

    /**
     * @return mixed
     */
    public function isEnabled(){
        return $this->_helperData->isEnabled();
    }
}
