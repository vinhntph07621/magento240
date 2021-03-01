<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-06-03
 * Time: 14:02
 */
namespace Omnyfy\Vendor\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Omnyfy\Vendor\Api\Data\VendorTypeInterface;
use Omnyfy\Vendor\Api\Data\VendorTypeSearchResultInterface;


class VendorTypeRepository implements \Omnyfy\Vendor\Api\VendorTypeRepositoryInterface
{
    protected $vendorTypeFactory;

    protected $vendorTypeCollectionFactory;

    protected $searchResultFactory;

    protected $searchCriteriaBuilder;

    protected $instances = [];

    public function __construct(
        \Omnyfy\Vendor\Model\VendorTypeFactory $vendorTypeFactory,
        \Omnyfy\Vendor\Model\Resource\VendorType\CollectionFactory $vendorTypeCollectionFactory,
        \Omnyfy\Vendor\Api\Data\VendorTypeSearchResultInterfaceFactory $searchResultFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->vendorTypeFactory = $vendorTypeFactory;
        $this->vendorTypeCollectionFactory = $vendorTypeCollectionFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function save(VendorTypeInterface $vendorType)
    {
        return true;
    }

    public function delete(VendorTypeInterface $vendorType)
    {
        return true;
    }

    public function deleteById($vendorTypeId)
    {
        return true;
    }

    public function getById($vendorTypeId, $forceReload = false)
    {
        if (!isset($this->instances[$vendorTypeId]) || $forceReload) {
            $vendorType = $this->vendorTypeFactory->create();
            $vendorType->load($vendorTypeId);
            if (!$vendorType->getId()) {
                throw new NoSuchEntityException(__('Request Vendor Type doesn\'t exist'));
            }
            $this->instances[$vendorTypeId] = $vendorType;
        }
        return $this->instances[$vendorTypeId];
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->vendorTypeCollectionFactory->create();

        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $fields = [];
            foreach ($group->getFilters() as $filter) {
                $conditionType = $filter->getConditionType() ? $filter->getConditionType() : 'eq';

                $fields[] = ['attribute' => $filter->getField(), $conditionType => $filter->getValue()];
            }

            if ($fields) {
                $collection->addFieldToFilter($fields);
            }
        }

        /** @var SortOrder $sortOrder */
        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $field = $sortOrder->getField();
            $collection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->load();

        $searchResult = $this->searchResultFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }
}
 