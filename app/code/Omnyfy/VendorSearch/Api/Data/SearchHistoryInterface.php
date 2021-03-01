<?php


namespace Omnyfy\VendorSearch\Api\Data;

interface SearchHistoryInterface
{

    const ATTRIBUTE_VALUE_ID_1 = 'attribute_value_id_1';
    const LOCATION = 'location';
    const ATTRIBUTE_VALUE_2 = 'attribute_value_2';
    const ATTRIBUTE_VALUE_ID_2 = 'attribute_value_id_2';
    const ATTRIBUTE_VALUE_1 = 'attribute_value_1';
    const SEARCHHISTORY_ID = 'searchhistory_id';
    const SEARCH_DATE = 'search_date';
    const SEARCH_STRING = 'search_string';


    /**
     * Get searchhistory_id
     * @return string|null
     */
    public function getSearchhistoryId();

    /**
     * Set searchhistory_id
     * @param string $searchhistoryId
     * @return \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface
     */
    public function setSearchhistoryId($searchhistoryId);

    /**
     * Get location
     * @return string|null
     */
    public function getLocation();

    /**
     * Set location
     * @param string $location
     * @return \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface
     */
    public function setLocation($location);

    /**
     * Get attribute_value_id_1
     * @return string|null
     */
    public function getAttributeValueId1();

    /**
     * Set attribute_value_id_1
     * @param string $attributeValueId1
     * @return \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface
     */
    public function setAttributeValueId1($attributeValueId1);

    /**
     * Get attribute_value_1
     * @return string|null
     */
    public function getAttributeValue1();

    /**
     * Set attribute_value_1
     * @param string $attributeValue1
     * @return \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface
     */
    public function setAttributeValue1($attributeValue1);

    /**
     * Get attribute_value_id_2
     * @return string|null
     */
    public function getAttributeValueId2();

    /**
     * Set attribute_value_id_2
     * @param string $attributeValueId2
     * @return \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface
     */
    public function setAttributeValueId2($attributeValueId2);

    /**
     * Get search_string
     * @return string|null
     */
    public function getSearchString();

    /**
     * Set search_string
     * @param string $searchString
     * @return \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface
     */
    public function setSearchString($searchString);

    /**
     * Get search_date
     * @return string|null
     */
    public function getSearchDate();

    /**
     * Set search_date
     * @param string $searchDate
     * @return \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface
     */
    public function setSearchDate($searchDate);

    /**
     * Get attribute_value_2
     * @return string|null
     */
    public function getAttributeValue2();

    /**
     * Set attribute_value_2
     * @param string $attributeValue2
     * @return \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface
     */
    public function setAttributeValue2($attributeValue2);
}
