<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 10/7/17
 * Time: 2:33 PM
 */
namespace Omnyfy\Vendor\Model;

use Omnyfy\Vendor\Api\Data\VendorInterface;
use Omnyfy\Vendor\Api\VendorRepositoryInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;

class VendorRepository implements VendorRepositoryInterface
{

    /**
     * @var VendorFactory
     */
    protected $vendorFactory;

    /**
     * @var \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Omnyfy\Vendor\Api\Data\VendorSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    protected $metadataService;

    protected $searchCriteriaBuilder;

    protected $instances = [];

    public function __construct(
        VendorFactory $vendorFactory,
        \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $collectionFactory,
        \Omnyfy\Vendor\Api\Data\VendorSearchResultsInterfaceFactory $searchResultsFactory,
        \Omnyfy\Vendor\Api\VendorAttributeRepositoryInterface $metadataService,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->vendorFactory = $vendorFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->metadataService = $metadataService;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function save(VendorInterface $vendor)
    {
        // TODO: Implement save() method.
        return true;
    }

    public function delete(VendorInterface $vendor)
    {
        // TODO: Implement delete() method.
        return true;
    }

    public function deleteById($vendorId)
    {
        // TODO: Implement deleteById() method.
        return true;
    }

    public function getById($vendorId, $forceReload = false)
    {
        if (!isset($this->instances[$vendorId]) || $forceReload) {
            $vendor = $this->vendorFactory->create();
            $vendor->load($vendorId);
            if (!$vendor->getId()) {
                throw new NoSuchEntityException(__('Requested Vendor doesn\'t exist'));
            }
            $this->instances[$vendorId] = $vendor;
        }
        return $this->instances[$vendorId];
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        foreach ($this->metadataService->getList($this->searchCriteriaBuilder->create())->getItems() as $metadata) {
            $collection->addAttributeToSelect($metadata->getAttributeCode());
        }

        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $fields = [];
            $longitude = $latitude = $distance = null;
            foreach ($group->getFilters() as $filter) {
                $conditionType = $filter->getConditionType() ? $filter->getConditionType() : 'eq';

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

        /** @var SortOrder $sortOrder */
        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $field = $sortOrder->getField();
            $direction = ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC';

            //Only when distance in filter can sort by distance
            if ('distance' == $field) {
                if($collection->getFlag('has_distance_filter')) {

                    $collection->getSelect()->order('l.distance ' . $direction);
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
}