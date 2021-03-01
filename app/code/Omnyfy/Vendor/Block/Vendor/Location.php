<?php
/**
 * Project: Multi Vendor M2.
 * User: abhay
 * Date: 15/3/18
 * Time: 3:54 PM
 */
namespace Omnyfy\Vendor\Block\Vendor;

use Magento\Framework\View\Element\Template;

class Location extends \Magento\Framework\View\Element\Template
{
    protected $coreRegistry;
	protected $providerFactory;
    protected $locationFactory;
    protected $vendorFactory;
	protected $collectionFactory;
    protected $locationCollectionFactory;
    protected $locationMetadataService;
    protected $searchCriteriaBuilder;

    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Omnyfy\Vendor\Model\LocationFactory $locationFactory,
		\Magento\Customer\Model\Session $customerSession,
        \Omnyfy\Vendor\Model\VendorFactory $vendorFactory,
        \Omnyfy\Vendor\Model\Resource\Location\CollectionFactory $locationCollectionFactory,
        \Omnyfy\Vendor\Api\LocationAttributeRepositoryInterface $locationMetadataService,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = [])
    {
        $this->coreRegistry = $coreRegistry;
		$this->collectionFactory = $collectionFactory;
        $this->locationFactory = $locationFactory;
		$this->customerSession = $customerSession;
        $this->vendorFactory = $vendorFactory;
        $this->locationCollectionFactory = $locationCollectionFactory;
        $this->locationMetadataService = $locationMetadataService;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context, $data);
    }

    public function getVendor(){
        $product = $this->coreRegistry->registry('product');
        if (empty($product) || $product->getId() ==0) {
            return false;
        }

        $vendor = $this->locationFactory->create();

        $vendorId = $vendor->getResource()->getVendorIdByProductId($product->getId());
        if (empty($vendorId)) {
            return false;
        }
        $vendor->load($vendorId);
        if ($vendor->getId() == $vendorId) {
            return $vendor;
        }

        // load the vendor by current location
        return false;
    }

    public function getBannerUrl($vendorBanner)
    {
        if (empty($vendorBanner)) {
            return false;
        }
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $vendorBanner;
    }

    public function getLogoUrl($vendorLogo)
    {
        if (empty($vendorLogo)) {
            return false;
        }
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $vendorLogo;
    }

    public function getVendorUrl($vendor){
        return $this->getUrl('omnyfy_vendor/brands/view', ['id' => $vendor->getId()]);
    }
	
	public function getLocationDetails(){
		$locationId = $this->getRequest()->getParam('id');
		return $this->locationFactory->create()->load($locationId);
	}
	
    public function getVendorDetails($vendorId)
    {
        $vendor = $this->vendorFactory->create()->load($vendorId);
        $this->coreRegistry->register('vendor', $vendor);
		return $vendor;
	}
	
	public function getAllLocations($vendorId){
		$locationId = $this->getRequest()->getParam('id');
		$locationCollection = $this->locationFactory->create()->getCollection()
                                ->addFieldToSelect('*')
                                ->addFieldToFilter('vendor_id', $vendorId)
                                ->addFieldToFilter('entity_id',array('neq'=>$locationId))
                                ->addFieldToFilter('status','1')
        ;
		return $locationCollection;
	}
	
	public function getOpeningHours($openingHours)
    {
        return json_decode($openingHours, true);
    }
	
	/* public function getProviderCollection(){
		$locationId = $this->getRequest()->getParam('id');
		$collection = $this->providerFactory->create()->getCollection();
         
        $collection->getSelect()->joinLeft(array('second' => 'omnyfy_booking_provider_location'),
                                               'e.entity_id = second.provider_id')
											   ->where("second.location_id=".$locationId); 
																								
		return $collection;
	} */
	
	/**
     * Return login url for guest users with referer url
     *
     * @return string
     */
    public function getLoginUrl() {
        $url = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
        $login_url = $this->getUrl('customer/account/login', array('referer' => base64_encode($url)));
        return $login_url;
    }
	
	public function isLoggedIn() {
        return $this->customerSession->isLoggedIn();
    }
	
	public function getServicesCollection(){
		$locationId = $this->getRequest()->getParam('id');
		$collection = $this->collectionFactory->create();                             
		$joinConditions = 'e.entity_id = omnyfy_vendor_inventory.product_id';
		$collection->addAttributeToSelect('*');
		$collection->getSelect()->join(
					 ['omnyfy_vendor_inventory'],
					 $joinConditions,
					 []
					)->columns("omnyfy_vendor_inventory.location_id")
					  ->where("omnyfy_vendor_inventory.location_id=".$locationId);	
		return $collection;
	}

    public function loadLocationAttributes()
    {
        $locationId = (int)$this->getRequest()->getParam('id');
        $vendorLocation = $this->vendorFactory->create();
        if ($locationId) {
            try {
                $vendorLocation->load($locationId);
            }
            catch (\Exception $e) {
                $this->_logger->critical($e);
            }
        }

        $collection = $this->locationCollectionFactory->create()
            ->addFieldToFilter('entity_id', $vendorLocation->getEntityId());

        foreach ($this->locationMetadataService->getList($this->searchCriteriaBuilder->create())->getItems() as $metadata) {
            if ($metadata['is_visible_on_front']) {
                $collection->addFieldToSelect($metadata->getAttributeCode());
            }
        }

        return $collection->getFirstItem();
    }

    public function getColumnSplitValue($attributes)
    {
        // @TODO - put into configuration value
        $excludeFields = ['entity_id', 'vendor_id', 'priority', 'location_name', 'description', 'address', 'suburb', 'region', 'country', 'postcode', 'lat', 'lon', 'rad_lon', 'rad_lat', 'cos_lat', 'sin_lat', 'region_id', 'status', 'booking_lead_time', 'timezone', 'is_warehouse', 'vendor_type_id', 'attribute_set_id'];
        $getColumnSplitValue = 0;
        foreach ($attributes as $attributeKey => $attributeValue) {
            if (!in_array($attributeKey, $excludeFields)) {
                if ($this->shouldDisplayAttribute($attributeKey)) {
                    $getColumnSplitValue++;
                }
            }
        }

        return $getColumnSplitValue;
    }

    public function getMultiSelectValues($attribute)
    {
        $location = $this->getLocationDetails();

        return explode(",", $location->getResource()->getAttribute($attribute)->getFrontEnd()->getValue($location));
    }

    public function shouldDisplayAttribute($attribute)
    {
        if ($this->getLocationDetails()->getResource()->getAttribute($attribute)) {
            return true;
        }
        else {
            return false;
        }
    }

    public function getAttributeLabel($attribute)
    {
        if ($this->getLocationDetails()->getResource()->getAttribute($attribute)) {
            $attributeData = $this->getLocationDetails()->getResource()->getAttribute($attribute)->getData();

            if (isset($attributeData['frontend_label'])) {
                return $attributeData['frontend_label'];
            }
        }
        else {
            return '';
        }
    }

    public function getAttributeValue($attribute)
    {
        $location = $this->getLocationDetails();

        return $location->getResource()->getAttribute($attribute)->getFrontEnd()->getValue($location);
    }

    public function isAttributeMultiselect($attribute)
    {
        $location = $this->getLocationDetails();

        $attributeInfo = $location->getResource()->getAttribute($attribute);

        if (!empty($attributeInfo)) {
            $attributeInfo = $attributeInfo->getData();

            if ($attributeInfo['frontend_input'] == 'multiselect') {
                return true;
            }
        }

        return false;
    }
}
