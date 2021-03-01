<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-customer-segment
 * @version   1.0.51
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CustomerSegment\Repository\Segment;

use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Model\Segment\CustomerFactory as SegmentCustomerFactory;
use Mirasvit\CustomerSegment\Model\ResourceModel\Segment\Customer\Collection;
use Mirasvit\CustomerSegment\Model\ResourceModel\Segment\Customer\CollectionFactory;
use Mirasvit\CustomerSegment\Api\Data\Segment\CustomerInterface as SegmentCustomerInterface;
use Mirasvit\CustomerSegment\Api\Repository\Segment\CustomerRepositoryInterface;
use Mirasvit\CustomerSegment\Api\Data\Segment\CustomerSearchResultsInterface;
use Mirasvit\CustomerSegment\Api\Data\Segment\CustomerSearchResultsInterfaceFactory;

class CustomerRepository implements CustomerRepositoryInterface
{
    /**
     * @var SegmentCustomerInterface[]
     */
    private $entities = [];
    /**
     * @var CustomerSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;
    /**
     * @var SegmentCustomerFactory
     */
    private $segmentCustomerFactory;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * CustomerRepository constructor.
     *
     * @param CustomerSearchResultsInterfaceFactory $searchResultsFactory
     * @param SegmentCustomerFactory                $segmentCustomerFactory
     * @param CollectionFactory                     $collectionFactory
     */
    public function __construct(
        CustomerSearchResultsInterfaceFactory $searchResultsFactory,
        SegmentCustomerFactory $segmentCustomerFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->segmentCustomerFactory = $segmentCustomerFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        if (isset($this->entities[$id])) {
            return $this->entities[$id];
        }

        /** @var SegmentCustomerInterface $segmentCustomer */
        $segmentCustomer = $this->segmentCustomerFactory->create()->load($id);
        if (!$segmentCustomer->getId()) {
            throw NoSuchEntityException::singleField(SegmentCustomerInterface::ID, $id);
        }

        $this->entities[$segmentCustomer->getId()] = $segmentCustomer;

        return $segmentCustomer;
    }

    /**
     * {@inheritDoc}
     */
    public function save(SegmentCustomerInterface $segmentCustomer)
    {
        $dateTime = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        if (!$segmentCustomer->getId()) {
            $segmentCustomer->setCreatedAt($dateTime);
        }

        return $segmentCustomer->save();
    }

    /**
     * {@inheritDoc}
     */
    public function delete(SegmentCustomerInterface $segmentCustomer)
    {
        return $segmentCustomer->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function deleteById($customerId)
    {
        return $this->delete($this->get($customerId));
    }

    /**
     * {@inheritDoc}
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->segmentCustomerFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var CustomerSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $collection = $this->getCollection();
        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }

        $searchResults->setTotalCount($collection->getSize());
        //Add sort order from search criteria sort orders to the collection
        if ($searchCriteria->getSortOrders()) {
            /** @var SortOrder $sortOrder */
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? SortOrder::SORT_ASC : SortOrder::SORT_DESC
                );
            }
        }

        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     *
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $collection)
    {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            switch ($filter->getField()) {
                case SegmentInterface::WEBSITE_ID:
                    $table = 'segment.';
                    $this->joinSegmentTable($collection);
                    break;
                // Unknown column 'main_table.status' in 'where clause'
                case SegmentInterface::STATUS:
                    $table = 'segment.';
                    break;
                default:
                    $table = 'main_table.';
            }

            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = $table . $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }

        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * Join mst_customersegment_segment table to customer_segment collection.
     *
     * @param Collection $collection
     *
     * @return void
     */
    private function joinSegmentTable(Collection $collection)
    {
        $collection->getSelect()
            ->joinLeft(
                ['segment' => $collection->getTable('mst_customersegment_segment')],
                'segment.segment_id = main_table.segment_id',
                ['website_id']
            );
    }
}
