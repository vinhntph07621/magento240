<?php


namespace Omnyfy\VendorSearch\Block\Search;

class Result extends \Magento\Framework\View\Element\Template
{
    /** @var \Magento\Framework\Api\SearchCriteriaBuilder  */
    protected $_searchCriteriaBuilder;

    /** @var \Omnyfy\LayeredNavigation\Model\Layer\Resolver  */
    protected $_layer;

    /** @var \Omnyfy\Vendor\Api\VendorTypeRepositoryInterface  */
    protected $_vendorTypeRepository;

    /** @var \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory  */
    protected $_vendorCollectionFactory;

    /** @var \Omnyfy\Vendor\Helper\Media  */
    protected $_vendorMedia;

    /** @var \Omnyfy\Vendor\Api\VendorRepositoryInterface  */
    protected $_vendorRepository;

    /** @var \Omnyfy\Vendor\Api\VendorAttributeManagementInterface  */
    protected $_attributeManagement;

    /** @var \Omnyfy\Vendor\Api\VendorAttributeRepositoryInterface  */
    protected $_vendorMetadataService;

    /**
     * @var \Omnyfy\VendorSearch\Helper\Data
     */
    protected $_helperData;
    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Omnyfy\LayeredNavigation\Model\Layer\Resolver $layerResolver,
        \Omnyfy\Vendor\Api\VendorTypeRepositoryInterface $vendorTypeRepository,
        \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory,
        \Omnyfy\Vendor\Helper\Media $vendorMedia,
        \Omnyfy\Vendor\Api\VendorRepositoryInterface $vendorRepository,
        \Omnyfy\Vendor\Api\VendorAttributeManagementInterface $attributeManagement,
        \Omnyfy\Vendor\Api\VendorAttributeRepositoryInterface $vendorMetadataService,
        \Omnyfy\VendorSearch\Helper\Data $helperData,
        array $data = []
    ) {
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_vendorTypeRepository = $vendorTypeRepository;
        $this->_vendorCollectionFactory = $vendorCollectionFactory;
        $this->_vendorMedia = $vendorMedia;
        $this->_vendorRepository = $vendorRepository;
        $this->_attributeManagement = $attributeManagement;
        $this->_vendorMetadataService = $vendorMetadataService;
        $this->_helperData = $helperData;
        try{
            $layerResolver->create('omnyfy_vendorsearch_provider');
        } catch (\Exception $exception)
        {

        }
        $this->_layer = $layerResolver->get();

        parent::__construct($context, $data);
    }


    /**
     * @return \Omnyfy\LayeredNavigation\Model\AbstractLayer|\Omnyfy\LayeredNavigation\Model\Layer\Resolver
     */
    public function getLayer(){
        return $this->_layer;
    }

    /**
     * @return \Omnyfy\LayeredNavigation\Model\Layer\Filter\AbstractFilter[]
     */
    public function getFilters(){
        return $this->getLayer()->getFilters();
    }

    /**
     * Retrieve active filters
     *
     * @return array
     */
    public function getActiveFilters()
    {
        $activeFilters = [];
        $filters = $this->getFilters();
        if (!is_array($filters)) {
            $filters = [];
        } else {
            foreach ($filters as $filter) {
                if ($filter->getItemsCount()) {
                    $filterItems = $filter->getItems();
                    foreach ($filterItems as $item) {
                        if ($item->isSelected()) {
                            $itemObject = array(
                                "filterName" => $filter->getName(),
                                "label" => $item->getLabel(),
                                "clearLinkText" => $filter->getClearLinkText(),
                                "clearLinkUrl" => $filter->getClearLinkUrl()
                            );

                            $activeFilters[] = $itemObject;
                        }
                    }
                }
            }
        }

        return $activeFilters;
    }

    /**
     * @return string
     */
    public function getVendors()
    {
        $collection = $this->getLayer()->getCollection();
        return $collection;
    }

    public function getVendorType(){
        return $this->getRequest()->getParam("type");
    }

    public function getDistance(){
        return $this->getRequest()->getParam("distance")?$this->getRequest()->getParam("distance"):5;
    }

    public function getSortOrder(){
        return $this->getRequest()->getParam("sort");
    }

    public function getSearchCount(){
        return $this->getLayer()->getCount();
    }

    public function getVendorTypeName(){
        return $this->getLayer()->getVendorTypeName();
    }

    public function getVendorViewMode(){
        return $this->getLayer()->getVendorViewMode();
    }

    public function getLocationUrl($locationId){
        $url = $this->_helperData->getLocationUrl();
        return $this->getUrl($url,['id' => $locationId]);
    }

    public function isSearchByLocation(){
        try {
            $vendorTypeId = $this->getVendorType();
            $vendorType = $this->_vendorTypeRepository->getById($vendorTypeId, true);
            return $vendorType->getSearchBy();
        }catch (\Exception $exception){
            return false;
        }
    }

    public function getImage($vendorId){
        try {
            $vendor = $this->_vendorRepository->getById($vendorId);
            if ($vendor) {
                return $this->_vendorMedia->getVendorLogoUrl($vendor);
            }
        }catch(\Exception $exception){
            $this->_logger->debug($exception->getMessage());
        }
        return "";
    }

    public function getLocationVendorData($vendorId)
    {
        try {
            /** @var \Omnyfy\Vendor\Model\Resource\Vendor\Collection $vendorCollection */
            $vendorCollection = $this->_vendorCollectionFactory->create();
            $vendorCollection->addAttributeToSelect('entity_id',["eq",$vendorId]);

            if ($vendorCollection->count() == 1){
                /** @var \Omnyfy\Vendor\Model\Vendor $vendor */
                $vendor = $vendorCollection->getFirstItem();
                return $this->getVendorData($vendor);
            }

        } catch (\Exception $exception){
            $this->_logger->debug($exception->getMessage());
        }
        return null;
    }

    /**
     * @param \Omnyfy\Vendor\Model\Vendor $vendor
     * @return array
     */
    public function getVendorData($vendor){
        $this->_logger->debug("get vendor attributes for vendor".$vendor->getId());
        try {
            $vendorTypeId = $this->getVendorType();
            $vendorType = $this->_vendorTypeRepository->getById($vendorTypeId, true);

            $data = [];

            if (!$vendorType->getSearchBy()){
                $this->_logger->debug("Search by ");
                $getVendorAttributeSetId = $vendorType->getVendorAttributeSetId();

                /** @var \Magento\Framework\Api\SearchCriteria $searchCriteria */
                $searchCriteria = $this->_searchCriteriaBuilder->addFilter('attribute_set_id',$getVendorAttributeSetId);
                $attributes = $this->_vendorMetadataService->getList($searchCriteria->create())->getItems();

                /** @var \Omnyfy\Vendor\Api\Data\VendorAttributeInterface $attribute */
                foreach($attributes as $attribute){

                    if ($attribute->getIsVisibleOnFront()){
                        $data[$attribute->getAttributeId()]['id'] = $attribute->getAttributeId();
                        $data[$attribute->getAttributeId()]['code'] = $attribute->getAttributeCode();
                        $data[$attribute->getAttributeId()]['label'] = $attribute->getDefaultFrontendLabel();
                        $data[$attribute->getAttributeId()]['type'] = $attribute->getFrontendInput();

                        if ($vendor) {
                            $customerAttribute = $vendor->getData($attribute->getAttributeCode());
                            if ($customerAttribute) {
                                if ($attribute->getFrontendInput() == "text")
                                    $data[$attribute->getAttributeId()]['data'] = $vendor->getResource()->getAttribute($attribute)->getFrontEnd()->getValue($vendor);

                                if ($attribute->getFrontendInput() == "multiselect"){
                                    $data[$attribute->getAttributeId()]['data'] =
                                        explode(",",$vendor->getResource()->getAttribute($attribute)->getFrontEnd()->getValue($vendor));
                                }
                            }
                        }
                    }
                }
            }

            return $data;
        } catch (\Exception $exception){
            $this->_logger->debug($exception->getMessage());
            return [];
        }
    }

    /**
     * @param $vendorId
     * @return null|\Omnyfy\Vendor\Api\Data\VendorInterface
     */
    public function getVendor($vendorId){
        try {
            /** @var \Omnyfy\Vendor\Api\Data\VendorInterface $vendor */
            $vendor = $this->_vendorRepository->getById($vendorId);
            return $vendor;
        } catch (\Exception $exception){
            return null;
        }
    }

    /**
     * @return mixed
     */
    public function isDistance(){
        return $this->_helperData->isDistance();
    }

}
