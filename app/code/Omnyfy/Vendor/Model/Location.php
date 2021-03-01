<?php

namespace Omnyfy\Vendor\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use Omnyfy\Vendor\Api\Data\LocationInterface;
use Omnyfy\Vendor\Api\VendorRepositoryInterface;

class Location extends AbstractExtensibleModel implements LocationInterface, IdentityInterface
{

    const CACHE_TAG = 'omnyfy_vendor_location';
    const ENTITY = 'omnyfy_vendor_location';

    protected $_eventObject = 'location';

    protected $customAttributesCodes = null;

    protected $metadataService;

    protected $interfaceAttributes = [
        LocationInterface::LOCATION_NAME,
        LocationInterface::VENDOR_ID,
        LocationInterface::VENDOR_NAME,
        LocationInterface::STATUS,
        LocationInterface::LATITUDE,
        LocationInterface::LONGITUDE,
        LocationInterface::PRIORITY,
        LocationInterface::DESCRIPTION,
        LocationInterface::VENDOR_TYPE_ID,
        LocationInterface::ATTRIBUTE_SET_ID
    ];

    /**
     * @var \Omnyfy\Vendor\Model\Indexer\Location\Flat\Processor
     */
    protected $_flatIndexerProcessor;

    /**
     * @var \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory
     */
    protected $_vendorCollectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_timezone;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Omnyfy\Vendor\Model\Vendor
     */
    protected $_vendor;

    protected $vendorRepository;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Omnyfy\Vendor\Model\Indexer\Location\Flat\Processor $flatIndexerProcessor
     * @param \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Omnyfy\Vendor\Model\Indexer\Location\Flat\Processor $flatIndexerProcessor,
        \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Omnyfy\Vendor\Api\VendorRepositoryInterface $vendorRepository,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );

        $this->_flatIndexerProcessor = $flatIndexerProcessor;
        $this->_vendorCollectionFactory = $vendorCollectionFactory;
        $this->_timezone = $timezone;
        $this->_urlBuilder = $urlBuilder;
        $this->vendorRepository = $vendorRepository;
    }

    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\Vendor\Model\Resource\Location');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get location name
     *
     * @return string|null
     */
    public function getLocationName()
    {
        return $this->getData(self::LOCATION_NAME);
    }

    /**
     * Set location name
     *
     * @param string $name
     * @return $this
     */
    public function setLocationName($name)
    {
        return $this->setData(self::LOCATION_NAME, $name);
    }

    /**
     * Get vendor id
     *
     * @return int|null
     */
    public function getVendorId()
    {
        return $this->getData(self::VENDOR_ID);
    }

    /**
     * Set vendor id
     *
     * @param int $vendorId
     * @return $this
     */
    public function setVendorId($vendorId)
    {
        return $this->setData(self::VENDOR_ID, $vendorId);
    }

    /**
     * Get priority
     *
     * @return int|null
     */
    public function getPriority()
    {
        return $this->getData(self::PRIORITY);
    }

    /**
     * Set priority
     *
     * @param int $priority
     * @return $this
     */
    public function setPriority($priority)
    {
        return $this->setData(self::PRIORITY, $priority);
    }

    /**
     * Get latitude
     *
     * @return float|null
     */
    public function getLatitude()
    {
        return $this->getData(self::LATITUDE);
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return $this
     */
    public function setLatitude($latitude)
    {
        return $this->setData(self::LATITUDE, $latitude);
    }

    /**
     * Get longitude
     *
     * @return float|null
     */
    public function getLongitude()
    {
        return $this->getData(self::LONGITUDE);
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return $this
     */
    public function setLongitude($longitude)
    {
        return $this->setData(self::LONGITUDE, $longitude);
    }

    /**
     * Get vendor name
     *
     * @return string|null
     */
    public function getVendorName()
    {
        return $this->getData(self::VENDOR_NAME);
    }

    /**
     * Set vendor name
     *
     * @param string $vendorName
     * @return $this
     */
    public function setVendorName($vendorName)
    {
        return $this->setData(self::VENDOR_NAME, $vendorName);
    }

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    public function getAddressFormatted($delim = '\n')
    {
        $address = $this->getData('address');
        $suburb = $this->getData('suburb');
        $region = $this->getData('region'); // TODO: Get region name
        $country = $this->getData('country'); // TODO: Get country name
        $postcode = $this->getData('postcode');

        $pieces = [];
        if (!empty($address)) $pieces[] = $address;
        if (!empty($suburb)) $pieces[] = $suburb;
        if (!empty($region)) $pieces[] = $region;
        if (!empty($country)) $pieces[] = $country;
        if (!empty($postcode)) $pieces[] = $postcode;

        return implode($delim, $pieces);
    }

    /**
     * After save
     *
     * @return $this
     */
    public function afterSave()
    {
        parent::afterSave();

        $this->_getResource()->addCommitCallback([$this, 'reindex']);

        return $this;
    }

    /**
     * Init indexing process after product save
     *
     * @return void
     */
    public function reindex()
    {
        $this->_flatIndexerProcessor->reindexRow($this->getId());
    }

    /**
     * Get vendor object
     *
     * @return \Omnyfy\Vendor\Model\Vendor
     */
    public function getVendor()
    {
        if (!($id = $this->getVendorId())) {
            return null;
        }

        if (!$this->_vendor) {
            $this->_vendor = $this->vendorRepository->getById($id);
        }

        return $this->_vendor;
    }

    public function getOptions($vendorId = null, $all=true)
    {
        $collection = $this->getCollection();

        if (!$all) {
            $collection->addFieldToFilter('status', \Omnyfy\Vendor\Api\Data\LocationInterface::STATUS_ENABLED);
        }

        if (!is_null($vendorId)) {
            $collection->addFieldToFilter('vendor_id', $vendorId);
        }
        $collection->setOrder('vendor_id');
        $result = [];
        foreach($collection as $location) {
            if (!isset($result[$location->getVendorId()])) {
                $result[$location->getVendorId()] = [];
            }
            $result[$location->getVendorId()][] = [
                'value' => $location->getId(),
                'label' => __($location->getLocationName()) . ' (' . $location->getSuburb() . ' ' . $location->getPostcode() . ')'
            ];
        }
        if (!is_null($vendorId) && isset($result[$vendorId])) {
            return $result[$vendorId];
        }
        return $result;
    }

    /**
     * Get current time at location
     *
     * @param string $format
     * @return mixed
     */
    public function getCurrentTime($format = 'U')
    {
        $timezone = $this->getTimezone();
        if (empty($timezone)) {
            return false;
        }

        $date = new \DateTime(
            $this->_timezone->date()->format('Y-m-d H:i:s'),
            new \DateTimeZone($this->_timezone->getConfigTimezone())
        );
        $date->setTimezone(new \DateTimeZone($timezone));

        return date($format, strtotime($date->format('Y-m-d H:i:s')));
    }

    /**
     * Get booking lead time
     *
     * @return int
     */
    public function getBookingLeadTime()
    {
        if ($blt = $this->getData('booking_lead_time')) {
            return $blt;
        }

        return $this->getVendor()->getData('booking_lead_time');
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getLocationName();
    }

    /**
     * Get link
     *
     * @return string|null
     */
    public function getLink()
    {
        return $this->_urlBuilder->getUrl('booking/practice/view/id/' . $this->getId(), ['_use_rewrite' => true]);
    }

    public function getIsWarehouse()
    {
        return $this->getData(self::IS_WAREHOUSE);
    }

    public function setIsWarehouse($isWarehouse)
    {
        $this->setData(self::IS_WAREHOUSE, $isWarehouse);

        return $this;
    }

    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus($status)
    {
        $this->setData(self::STATUS, $status);

        return $this;
    }

    public function getVendorTypeId()
    {
        return $this->getData(self::VENDOR_TYPE_ID);
    }

    public function setVendorTypeId($vendorTypeId)
    {
        return $this->setData(self::VENDOR_TYPE_ID, $vendorTypeId);
    }

    public function getAttributeSetId()
    {
        return $this->getData(self::ATTRIBUTE_SET_ID);
    }

    public function setAttributeSetId($attributeSetId)
    {
        return $this->setData(self::ATTRIBUTE_SET_ID, $attributeSetId);
    }

    public function getLocationContactName()
    {
        return $this->getData(self::LOCATION_CONTACT_NAME);
    }

    public function setLocationContactName($locationContactName)
    {
        return $this->setData(self::LOCATION_CONTACT_NAME, $locationContactName);
    }

    public function getLocationContactPhone()
    {
        return $this->getData(self::LOCATION_CONTACT_PHONE);
    }

    public function setLocationContactPhone($locationContactPhone)
    {
        return $this->setData(self::LOCATION_CONTACT_PHONE, $locationContactPhone);
    }

    public function getLocationContactEmail()
    {
        return $this->getData(self::LOCATION_CONTACT_EMAIL);
    }

    public function setLocationContactEmail($locationContactEmail)
    {
        return $this->setData(self::LOCATION_CONTACT_EMAIL, $locationContactEmail);
    }

    public function getLocationCompanyName()
    {
        return $this->getData(self::LOCATION_COMPANY_NAME);
    }

    public function setLocationCompanyName($locationCompanyName)
    {
        return $this->setData(self::LOCATION_COMPANY_NAME, $locationCompanyName);
    }

    public function getExtensionAttributes()
    {
        $extensionAttributes = $this->_getExtensionAttributes();
        if (!$extensionAttributes) {
            return $this->extensionAttributesFactory->create('Omnyfy\Vendor\Api\Data\LocationInterface');
        }
        return $extensionAttributes;
    }

    public function setExtensionAttributes($extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
