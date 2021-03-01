<?php

namespace Omnyfy\Postcode\Model;

use \Magento\Framework\DataObject\IdentityInterface;
use \Magento\Framework\Model\AbstractModel;
use \Omnyfy\Postcode\Api\Data\PostcodeInterface;

class Postcode extends AbstractModel implements PostcodeInterface, IdentityInterface
{

    const CACHE_TAG = 'omnyfy_postcode';
    const ENTITY = 'omnyfy_postcode';

    /**
     * @var \Magento\Directory\Model\Country
     */
    protected $_country;

    /**
     * @var \Magento\Directory\Model\Region
     */
    protected $_region;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $_countryFactory;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Region\CollectionFactory
     */
    protected $_regionCollectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_timezone;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );

        $this->_countryFactory = $countryFactory;
        $this->_regionCollectionFactory = $regionCollectionFactory;
        $this->_timezone = $timezone;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\Postcode\Model\ResourceModel\Postcode');
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function getCountryId()
    {
        return $this->getData(self::COUNTRY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCountryId($countryId)
    {
        $this->_country = null;
        $this->_region = null;
        return $this->setData(self::COUNTRY_ID, $countryId);
    }

    /**
     * {@inheritdoc}
     */
    public function getRegionCode()
    {
        return $this->getData(self::REGION_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setRegionCode($region)
    {
        $this->_region = null;
        return $this->setData(self::REGION_CODE, $region);
    }

    /**
     * {@inheritdoc}
     */
    public function getPostcode()
    {
        return $this->getData(self::POSTCODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPostcode($postcode)
    {
        return $this->setData(self::POSTCODE, $postcode);
    }

    /**
     * {@inheritdoc}
     */
    public function getSuburb()
    {
        return $this->getData(self::SUBURB);
    }

    /**
     * {@inheritdoc}
     */
    public function setSuburb($suburb)
    {
        return $this->setData(self::SUBURB, $suburb);
    }

    /**
     * {@inheritdoc}
     */
    public function getLatitude()
    {
        return $this->getData(self::LATITUDE);
    }

    /**
     * {@inheritdoc}
     */
    public function setLatitude($latitude)
    {
        return $this->setData(self::LATITUDE, $latitude);
    }

    /**
     * {@inheritdoc}
     */
    public function getLongitude()
    {
        return $this->getData(self::LONGITUDE);
    }

    /**
     * {@inheritdoc}
     */
    public function setLongitude($longitude)
    {
        return $this->setData(self::LONGITUDE, $longitude);
    }

    /**
     * {@inheritdoc}
     */
    public function getTimezone()
    {
        $timezone = $this->getData(self::TIMEZONE);
        if (!$timezone) {
            $timezone = $this->getRegion()->getData('timezone');
        }

        return $timezone;
    }

    /**
     * {@inheritdoc}
     */
    public function setTimezone($timezone)
    {
        return $this->setData(self::TIMEZONE, $timezone);
    }

    /**
     * Get country
     *
     * @return \Magento\Directory\Model\Country
     */
    public function getCountry()
    {
        if (!$this->_country) {
            $this->_country = $this->_countryFactory->create()->load($this->getCountryId());
        }

        return $this->_country;
    }

    /**
     * Get region
     *
     * @return \Magento\Directory\Model\Region
     */
    public function getRegion()
    {
        if (!$this->_region) {
            $collection = $this->_regionCollectionFactory->create();
            $collection->addCountryFilter($this->getCountryId());
            $collection->addRegionCodeFilter($this->getRegionCode());
            $this->_region = $collection->getFirstItem();
        }

        return $this->_region;
    }

    /**
     * Get current time at postcode
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

}
