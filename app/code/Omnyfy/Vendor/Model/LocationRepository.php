<?php

namespace Omnyfy\Vendor\Model;

use Omnyfy\Vendor\Api\Data\LocationInterface;
use Omnyfy\Vendor\Api\Data\LocationSearchResultsInterfaceFactory;
use Omnyfy\Vendor\Api\LocationRepositoryInterface;
use Omnyfy\Vendor\Model\Resource\Location\Collection;
use Omnyfy\Vendor\Model\Resource\Location\CollectionFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteriaInterface;

class LocationRepository implements LocationRepositoryInterface
{
    /**
     * @var \Omnyfy\Vendor\Model\LocationFactory
     */
    protected $locationFactory;

    /**
     * @var \Omnyfy\Vendor\Model\Resource\Location\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Omnyfy\Vendor\Api\Data\LocationSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    protected $metadataService;

    protected $searchCriteriaBuilder;

    /**
     * @var \Omnyfy\Vendor\Api\Data\LocationSimpleParameterSearchInterfaceFactory
     */
    protected $_simpleParamSearchFactory;

    /**
     * @var array
     */
    protected $instances = [];

    /**
     * Construct
     *
     * @param \Omnyfy\Vendor\Model\LocationFactory $locationFactory
     * @param CollectionFactory $collectionFactory
     * @param LocationSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Omnyfy\Vendor\Api\Data\LocationSimpleParameterSearchInterfaceFactory $simpleParamSearchFactory
     * @param \Omnyfy\Vendor\Api\LocationAttributeRepositoryInterface $metadataService
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        LocationFactory $locationFactory,
        CollectionFactory $collectionFactory,
        LocationSearchResultsInterfaceFactory $searchResultsFactory,
        \Omnyfy\Vendor\Api\Data\LocationSimpleParameterSearchInterfaceFactory $simpleParamSearchFactory,
        \Omnyfy\Vendor\Api\LocationAttributeRepositoryInterface $metadataService,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->locationFactory = $locationFactory;
        $this->_collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->metadataService = $metadataService;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_simpleParamSearchFactory = $simpleParamSearchFactory;
    }

    /**
     * Save
     *
     * @param \Omnyfy\Vendor\Api\Data\LocationInterface $location
     * @return [TODO]
     */
    public function save(LocationInterface $location)
    {

    }

    /**
     * Get by id
     *
     * @param int $locationId
     * @param bool $forceReload
     * @return array
     * @throws NoSuchEntityException
     */
    public function getById($locationId, $forceReload = false)
    {
        if (!isset($this->instances[$locationId]) || $forceReload) {
            $location = $this->locationFactory->create();
            $location->load($locationId);
            if (!$location->getId()) {
                throw new NoSuchEntityException(__('Requested location doesn\'t exist'));
            }
            $this->instances[$locationId] = $location;
        }
        return $this->instances[$locationId];
    }

    /**
     * Delete
     *
     * @param \Omnyfy\Vendor\Api\Data\LocationInterface $location
     * @return [TODO]
     */
    public function delete(LocationInterface $location)
    {

    }

    /**
     * Delete by id
     *
     * @param type $locationId
     * @return [TODO]
     */
    public function deleteById($locationId)
    {

    }

    /**
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return type
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->_collectionFactory->create();

        foreach ($this->metadataService->getList($this->searchCriteriaBuilder->create())->getItems() as $metadata) {
            $collection->addAttributeToSelect($metadata->getAttributeCode());
        }

        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $collection->joinVendorInfo();

        /** @var SortOrder $sortOrder */
        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $field = $sortOrder->getField();
            $direction = ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC';

            if ('distance' == $field) {
                if ($collection->getFlag('has_distance_filter')) {
                    $collection->getSelect()->order('distance ' . $direction);
                }
                continue;
            }

            $collection->addOrder($field, $direction);
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->load();

        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }
    
    /**
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return type
     */
    public function getListVendorWarehouse(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->_collectionFactory->create();
        
        foreach ($this->metadataService->getList($this->searchCriteriaBuilder->create())->getItems() as $metadata) {
            $collection->addAttributeToSelect($metadata->getAttributeCode());
        }
        
        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $collection->joinVendorInfo();
        
        /** @var SortOrder $sortOrder */
        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $field = $sortOrder->getField();
            $direction = ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC';
            
            if ('distance' == $field) {
                if ($collection->getFlag('has_distance_filter')) {
                    $collection->getSelect()->order('distance ' . $direction);
                }
                continue;
            }
            
            $collection->addOrder($field, $direction);
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->load();
        
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        
        $items = [];
        
        foreach ($collection->getItems() as $item){
            $items[] = [
            "id" => $item["entity_id"],
            "vendor_id" => $item["vendor_id"]
            ];
        }
        
        $searchResult->setItems($items);
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Omnyfy\Vendor\Model\Resource\Location\Collection $collection
     * @return void
     */
    protected function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        Collection $collection
    ) {
        $fields = [];
        $longitude = $latitude = $distance = null;
        foreach ($filterGroup->getFilters() as $filter) {
            $conditionType = $filter->getConditionType() ? $filter->getConditionType() : 'eq';

            if ($conditionType == 'in') {
                $collection->addFieldToFilter($filter->getField(), ['in' => explode(',', $filter->getValue())]);
                continue;
            }

            if ('distance' == $filter->getField()) {
                $distance = $filter->getValue();
                $distance = $distance < 1 ? 1 : $distance;
                continue;
            }

            if ('longitude' == $filter->getField()){
                $longitude = $filter->getValue();
                continue;
            }

            if('latitude' == $filter->getField()) {
                $latitude = $filter->getValue();
                continue;
            }

            $fields[] = ['attribute' => $filter->getField(), $conditionType => $filter->getValue()];
        }

        if (!empty($latitude) && !empty($longitude) && !empty($distance)) {
            $collection->addDistanceFilter($latitude, $longitude, $distance);
        }

        if ($fields) {
            $collection->addFieldToFilter($fields);
        }
    }

    /**
     * Get list by keyword
     *
     * @param string $keyword
     * @return \Omnyfy\Vendor\Api\Data\LocationSimpleParameterSearchInterface
     */
    public function getListByKeyword($keyword)
    {
        $collection = $this->_collectionFactory->create();
        /* @var $collection \Omnyfy\Vendor\Model\Resource\Location\Collection */

        $collection->addStatusFilter(1);
        $collection->addKeywordFilter($keyword);
        $collection->setPageSize(10);

        $searchResult = $this->_simpleParamSearchFactory->create();
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

}
