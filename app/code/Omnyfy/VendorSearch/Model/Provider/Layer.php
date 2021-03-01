<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 5/07/2019
 * Time: 4:39 PM
 */

namespace Omnyfy\VendorSearch\Model\Provider;


class Layer extends \Omnyfy\LayeredNavigation\Model\AbstractLayer
{
    const VIEW_GRID = 0;
    const VIEW_LIST = 1;
    /**
     * @var \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory
     */
    protected $_vendorCollectionFactory;

    /**
     * @var \Omnyfy\Vendor\Model\Resource\Location\CollectionFactory
     */
    protected $_locationCollectionFactory;

    /**
     * @var \Omnyfy\Vendor\Api\VendorTypeRepositoryInterface
     */
    protected $_vendorTypeRepository;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var
     */
    protected $_collection;


    protected $standardAttributes = [
        "type" => "type",
        "location" => "location",
        "sort" => "sort",
        "distance" => "distance",
        "project" => "project"
    ];

    /**
     * @var \Omnyfy\Postcode\Api\PostcodeRepositoryInterface
     */
    protected $_postcodeRepository;

    /**
     * @var \Omnyfy\Postcode\Model\ResourceModel\Postcode\CollectionFactory
     */
    protected $_postcodeCollectionRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $_vendorTypes = [];

    public function __construct(
        \Magento\Eav\Model\Entity\TypeFactory $entityTypeFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Omnyfy\LayeredNavigation\Model\Layer\StateFactory $stateFactory,
        \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory,
        \Omnyfy\Vendor\Model\Resource\Location\CollectionFactory $locationCollectionFactory,
        \Magento\Framework\App\Request\Http $request,
        \Omnyfy\Vendor\Api\VendorTypeRepositoryInterface $vendorTypeRepository,
        \Omnyfy\Postcode\Api\PostcodeRepositoryInterface $postcodeRepository,
        \Omnyfy\Postcode\Model\ResourceModel\Postcode\CollectionFactory $postcodeCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        array $data = [])
    {
        $this->_vendorCollectionFactory = $vendorCollectionFactory;
        $this->_locationCollectionFactory = $locationCollectionFactory;
        $this->_vendorTypeRepository = $vendorTypeRepository;
        $this->_request = $request;
        $this->_postcodeRepository = $postcodeRepository;
        $this->_postcodeCollectionRepository = $postcodeCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_logger = $logger;
        parent::__construct($entityTypeFactory, $objectManager, $stateFactory, $data);
    }

    /**
     * @var array
     */
    protected $_vendorEntityTypes = [
        'e' => [
            'code'              => \Omnyfy\Vendor\Model\Vendor::ENTITY,
            'attribute_model'   => '\Omnyfy\Vendor\Model\Resource\Vendor\Eav\Attribute',
        ],
    ];

    protected $_locationEntityTypes = [
        'e' => [
            'code'              => \Omnyfy\Vendor\Model\Location::ENTITY,
            'attribute_model'   => '\Omnyfy\Vendor\Model\Resource\Eav\Attribute',
        ],
    ];

    public function getFilterableAttributes()
    {
        if (!$this->_attributes) {
            if ($this->_isSearchByLocation()) {
                $this->_entityTypes = $this->_locationEntityTypes;
            }
            else {
                $this->_entityTypes = $this->_vendorEntityTypes;
            }
            foreach ($this->_entityTypes as $table => $type) {
                $entityType = $this->_entityTypeFactory->create()->loadByCode($type['code']);
                $collection = $entityType->getAttributeCollection();
                $collection->setItemObjectClass($type['attribute_model']);
                $collection->addFieldToFilter('additional_table.is_filterable', ['gt' => 0]);

                foreach ($collection as $attribute) {
                    $this->_attributes[$table][] = $attribute;
                }
            }
        }

        return $this->_attributes;
    }

    public function getCollection() {
        if ($this->_collection)
            return $this->_collection;

        $vendorTypeId = $this->_request->getParam("type");
        $postcode = $this->_request->getParam("location");
        $distance = $this->_request->getParam("distance")?$this->_request->getParam("distance"):5;

        $searchByLocation = $this->_isSearchByLocation();

        /** Select collection based on vendor type */
        /** Search by location */
        if ($searchByLocation) {
            $this->_collection = $this->_locationCollectionFactory->create()->addAttributeToSelect('*');

            /** Join vendor_profile table that handle the website id */
            $this->_collection->getSelect()->join(
                ['vp'=>'omnyfy_vendor_profile'],
                'e.vendor_id = vp.vendor_id',
                [
                    'website_id' => 'vp.website_id'
                ]
            );
        } else {
            $this->_collection = $this->_vendorCollectionFactory->create()->addAttributeToSelect('*');
            /** Join vendor_profile table that handle the website id */
            $this->_collection->getSelect()->join(
                ['vp'=>'omnyfy_vendor_profile'],
                'e.entity_id = vp.vendor_id',
                [
                    'website_id' => 'vp.website_id'
                ]
            );
        }

        /** Multi store support */
        try {
            $storeId = $this->_storeManager->getWebsite()->getId();
            $this->_collection->getSelect()->where("vp.website_id = " . $storeId);
        } catch(\Exception $exception){}


        $params = $this->_request->getParams();
        foreach($params as $key=>$values) {
            if (!$this->isKeyAttributes($key)) {
                $paramArray = array_filter(explode(",", $values));
                foreach ($paramArray as $param) {

                    try {
                        $this->_collection->addAttributeToFilter($key, ['like' => '%' . $param . '%']);
                    } catch (\Exception $exception) {

                    }
                }
            }
        }


        try {
            if ($vendorTypeId){
                if ($searchByLocation) {
                    $this->_collection->addAttributeToFilter("vendor_type_id", ['eq' => $vendorTypeId]);
                } else {
                    $this->_collection->addAttributeToFilter("type_id", ['eq' => $vendorTypeId]);
                }
            }

            $this->_collection->addAttributeToFilter("status", ['eq' => 1 ]);

            $postcodeInterface = $this->getPostcode($postcode);

            if ($postcodeInterface) {
                $latitude = $postcodeInterface->getData('latitude');
                $longitude = $postcodeInterface->getData('longitude');

                if (!empty($latitude) && !empty($longitude) && !empty($distance)) {
                    $this->_collection->addDistanceFilter($latitude, $longitude, $distance);
                }
            }
        } catch (\Exception $exception) {}

        /*Sort the result*/
        $sortField = $this->getSortOrder();
        $direction = "ASC";

        if ('distance' == $sortField) {
            if($this->_collection->getFlag('has_distance_filter')) {
                $this->_collection->getSelect()->order('distance ' . $direction);
            }
        } else {
            $this->_collection->addOrder($sortField, $direction);
        }

        if ($searchByLocation) {
            $this->_collection->getSelect()->columns(['location_count' => new \Zend_Db_Expr('1')]);
            $this->_collection->getSelect()->columns(['entity_name' => new \Zend_Db_Expr('e.location_name')]);
            $this->_collection->getSelect()->join(
                ['v'=>'omnyfy_vendor_vendor_entity'],
                'e.vendor_id = v.entity_id',
                [
                    'vendor_name' => 'v.name',
                    'vendor_id' => 'e.vendor_id'
                ]
            );

        } else {
            $this->_collection->getSelect()->columns(['entity_name' => new \Zend_Db_Expr('e.name')]);
        }

        return $this->_collection;
    }

    public function getLocations($vendorId){
        /** @var \Omnyfy\Vendor\Model\Resource\Vendor\Collection $locationCollection */
        $locationCollection = $this->_locationCollectionFactory->create();
        if ($vendorId){
            $locationCollection->addFieldToFilter("vendor_id",["eq" => $vendorId]);
        }
        $locationCollection->addFieldToFilter("status",["eq" => 1]);
        $locationCollection->load();
        return $locationCollection;
    }

    public function isKeyAttributes($attributeKey){
        if (array_key_exists($attributeKey,$this->standardAttributes))
            return true;

        return false;
    }

    /**
     * @param $postcode
     * @return null
     */
    public function getPostcode($postcode){
        $postcodeCollection = $this->_postcodeCollectionRepository->create();
        $postcodeCollection->addFieldToFilter('postcode',['eq' =>$postcode]);
        $postcodeCollection->load();

        if ($postcodeCollection->count() == 1)
            return $postcodeCollection->getFirstItem();

        return null;
    }

    /**
     * Get the total number of records in the collection
     * @return mixed
     */
    public function getCount(){
        if (!$this->_collection)
            $this->getCollection();

        return $this->_collection->count();
    }

    /**
     * Get the vendor type name
     * @return null|string
     */
    public function getVendorTypeName(){
        if ($vendorType = $this->getVendorType())
            return $vendorType->getTypeName();

        return "";
    }

    /**
     * Get the view mode for the vendor type
     * 0 - Grid view (defaulted)
     * 1 - List view
     * @return int|null|string
     */
    public function getVendorViewMode(){
        if ($vendorType = $this->getVendorType())
            return $vendorType->getViewMode();
        return self::VIEW_GRID;
    }

    /**
     * Get the vendorType by the url parameter
     * @return mixed|null|\Omnyfy\Vendor\Api\Data\VendorTypeInterface
     */
    public function getVendorType(){
        try {
            if ($vendorTypeId = $this->_request->getParam("type")) {
                if (isset($this->_vendorTypes[$vendorTypeId])) {
                    return $this->_vendorTypes[$vendorTypeId];
                }
                $vendorType = $this->_vendorTypeRepository->getById($vendorTypeId);
                $this->_vendorTypes[$vendorTypeId] = $vendorType;
                return $vendorType;
            }
        } catch(\Exception $exception){
            return null;
        }
    }

    /**
     * Get the sort by. name is the default value
     * @return mixed|string
     */
    public function getSortOrder(){
        return $this->_request->getParam("sort")?$this->_request->getParam("sort"):"name";
    }

    /**
     * Get the search by (vendor|location) configuration for the vendor Type
     * Default value is vendor (If the vendor Type is not defined)
     * 1 = use location search
     * 0 = use vendor search
     * @return int
     */
    protected function _getSearchBy()
    {
        $vendorType = $this->getVendorType();
        if (empty($vendorType)) {
            return \Omnyfy\Vendor\Model\Source\SearchBy::SEARCH_BY_VENDOR;
        }

        return $vendorType->getSearchBy();
    }

    /**
     * Check if the vendor type configured to search by location
     * @return int
     */
    protected function _isSearchByLocation()
    {
        if($this->_getSearchBy() == \Omnyfy\Vendor\Model\Source\SearchBy::SEARCH_BY_LOCATION)
            return \Omnyfy\Vendor\Model\Source\SearchBy::SEARCH_BY_LOCATION;

        return \Omnyfy\Vendor\Model\Source\SearchBy::SEARCH_BY_VENDOR;
    }
}