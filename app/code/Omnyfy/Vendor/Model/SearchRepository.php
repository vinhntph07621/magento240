<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-06-11
 * Time: 16:21
 */
namespace Omnyfy\Vendor\Model;

use Magento\Framework\Api\SortOrder;

class SearchRepository implements \Omnyfy\Vendor\Api\SearchRepositoryInterface
{
    /**
     * @var \Omnyfy\Vendor\Api\Data\VendorSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    protected $vendorTypeRepository;

    protected $vendorCollectionFactory;

    protected $locationCollectionFactory;

    protected $vendorMetadataService;

    protected $locationMetadataService;

    protected $searchCriteriaBuilder;

    public function __construct(
        \Omnyfy\Vendor\Api\VendorTypeRepositoryInterface $vendorTypeRepository,
        \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory,
        \Omnyfy\Vendor\Model\Resource\Location\CollectionFactory $locationCollectionFactory,
        \Omnyfy\Vendor\Api\Data\VendorSearchResultsInterfaceFactory $vendorSearchResultsFactory,
        \Omnyfy\Vendor\Api\VendorAttributeRepositoryInterface $vendorMetadataService,
        \Omnyfy\Vendor\Api\LocationAttributeRepositoryInterface $locationMetadataService,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->vendorTypeRepository = $vendorTypeRepository;
        $this->vendorCollectionFactory = $vendorCollectionFactory;
        $this->locationCollectionFactory = $locationCollectionFactory;
        $this->searchResultsFactory = $vendorSearchResultsFactory;
        $this->vendorMetadataService = $vendorMetadataService;
        $this->locationMetadataService = $locationMetadataService;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function getList($vendorTypeId, $searchCriteria)
    {
        //Load vendor type by vendor_type_id, if not found NoSuchEntityException will be thrown
        $vendorType = $this->vendorTypeRepository->getById($vendorTypeId);

        // TODO: init collection based on vendor_type's search_by settings
        $searchByLocation = $vendorType->getSearchBy();
        if ($searchByLocation) {
            $collection = $this->locationCollectionFactory->create();
            $collection->addFieldToFilter('vendor_type_id', $vendorTypeId);
            $collection->joinVendorInfo();

            foreach ($this->locationMetadataService->getList($this->searchCriteriaBuilder->create())->getItems() as $metadata) {
                $collection->addAttributeToSelect($metadata->getAttributeCode());
            }
        }
        else{
            $collection = $this->vendorCollectionFactory->create();
            $collection->addFieldToFilter('type_id', $vendorTypeId);

            foreach ($this->vendorMetadataService->getList($this->searchCriteriaBuilder->create())->getItems() as $metadata) {
                $collection->addAttributeToSelect($metadata->getAttributeCode());
            }
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
}
 