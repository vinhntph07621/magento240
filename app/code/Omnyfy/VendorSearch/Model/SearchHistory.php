<?php


namespace Omnyfy\VendorSearch\Model;

use Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface;

class SearchHistory extends \Magento\Framework\Model\AbstractModel implements SearchHistoryInterface
{

    protected $_eventPrefix = 'omnyfy_vendorsearch_searchhistory';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\VendorSearch\Model\ResourceModel\SearchHistory');
    }

    /**
     * Get searchhistory_id
     * @return string
     */
    public function getSearchhistoryId()
    {
        return $this->getData(self::SEARCHHISTORY_ID);
    }

    /**
     * Set searchhistory_id
     * @param string $searchhistoryId
     * @return \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface
     */
    public function setSearchhistoryId($searchhistoryId)
    {
        return $this->setData(self::SEARCHHISTORY_ID, $searchhistoryId);
    }

    /**
     * Get location
     * @return string
     */
    public function getLocation()
    {
        return $this->getData(self::LOCATION);
    }

    /**
     * Set location
     * @param string $location
     * @return \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface
     */
    public function setLocation($location)
    {
        return $this->setData(self::LOCATION, $location);
    }

    /**
     * Get attribute_value_id_1
     * @return string
     */
    public function getAttributeValueId1()
    {
        return $this->getData(self::ATTRIBUTE_VALUE_ID_1);
    }

    /**
     * Set attribute_value_id_1
     * @param string $attributeValueId1
     * @return \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface
     */
    public function setAttributeValueId1($attributeValueId1)
    {
        return $this->setData(self::ATTRIBUTE_VALUE_ID_1, $attributeValueId1);
    }

    /**
     * Get attribute_value_1
     * @return string
     */
    public function getAttributeValue1()
    {
        return $this->getData(self::ATTRIBUTE_VALUE_1);
    }

    /**
     * Set attribute_value_1
     * @param string $attributeValue1
     * @return \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface
     */
    public function setAttributeValue1($attributeValue1)
    {
        return $this->setData(self::ATTRIBUTE_VALUE_1, $attributeValue1);
    }

    /**
     * Get attribute_value_id_2
     * @return string
     */
    public function getAttributeValueId2()
    {
        return $this->getData(self::ATTRIBUTE_VALUE_ID_2);
    }

    /**
     * Set attribute_value_id_2
     * @param string $attributeValueId2
     * @return \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface
     */
    public function setAttributeValueId2($attributeValueId2)
    {
        return $this->setData(self::ATTRIBUTE_VALUE_ID_2, $attributeValueId2);
    }

    /**
     * Get search_string
     * @return string
     */
    public function getSearchString()
    {
        return $this->getData(self::SEARCH_STRING);
    }

    /**
     * Set search_string
     * @param string $searchString
     * @return \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface
     */
    public function setSearchString($searchString)
    {
        return $this->setData(self::SEARCH_STRING, $searchString);
    }

    /**
     * Get search_date
     * @return string
     */
    public function getSearchDate()
    {
        return $this->getData(self::SEARCH_DATE);
    }

    /**
     * Set search_date
     * @param string $searchDate
     * @return \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface
     */
    public function setSearchDate($searchDate)
    {
        return $this->setData(self::SEARCH_DATE, $searchDate);
    }

    /**
     * Get attribute_value_2
     * @return string
     */
    public function getAttributeValue2()
    {
        return $this->getData(self::ATTRIBUTE_VALUE_2);
    }

    /**
     * Set attribute_value_2
     * @param string $attributeValue2
     * @return \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface
     */
    public function setAttributeValue2($attributeValue2)
    {
        return $this->setData(self::ATTRIBUTE_VALUE_2, $attributeValue2);
    }
}
