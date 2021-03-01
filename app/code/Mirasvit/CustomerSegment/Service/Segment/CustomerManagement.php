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



namespace Mirasvit\CustomerSegment\Service\Segment;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ResourceConnection;
use Mirasvit\CustomerSegment\Api\Data\Segment\CustomerInterface as SegmentCustomerInterface;
use Mirasvit\CustomerSegment\Api\Data\Segment\HistoryInterface;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Api\Factory\Segment\ConditionFactoryInterface;
use Mirasvit\CustomerSegment\Api\Repository\SegmentRepositoryInterface;
use Mirasvit\CustomerSegment\Api\Service\Segment\CustomerManagementInterface;
use Mirasvit\CustomerSegment\Model\Segment\Condition\Customer\Attributes as CustomerCondition;
use Mirasvit\CustomerSegment\Repository\Segment\CustomerRepository as SegmentCustomerRepository;
use Mirasvit\CustomerSegment\Service\Segment\History\Writer;

class CustomerManagement implements CustomerManagementInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var SegmentCustomerRepository
     */
    private $segmentCustomerRepository;

    /**
     * @var SegmentRepositoryInterface
     */
    private $segmentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var ConditionFactoryInterface
     */
    private $conditionFactory;

    /**
     * CustomerManagement constructor.
     * @param ConditionFactoryInterface $conditionFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CustomerRepositoryInterface $customerRepository
     * @param SegmentCustomerRepository $segmentCustomerRepository
     * @param SegmentRepositoryInterface $segmentRepository
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ConditionFactoryInterface $conditionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerRepositoryInterface $customerRepository,
        SegmentCustomerRepository $segmentCustomerRepository,
        SegmentRepositoryInterface $segmentRepository,
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection        = $resourceConnection;
        $this->segmentCustomerRepository = $segmentCustomerRepository;
        $this->segmentRepository         = $segmentRepository;
        $this->searchCriteriaBuilder     = $searchCriteriaBuilder;
        $this->customerRepository        = $customerRepository;
        $this->conditionFactory          = $conditionFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function addSegmentCustomers(array $candidates, $segmentId, $remove = true)
    {
        $data          = [];
        $connection    = $this->resourceConnection->getConnection();
        $affectedCount = 0;
        $removedCount  = 0;

        if (!empty($candidates)) {
            /** @var SegmentInterface $candidate */
            foreach ($candidates as $candidate) {
                $data[] = $candidate->getData(); // Collect data to insert
            }

            // only new candidates are affected (duplicate records are not count)
            $affectedCount = $connection->insertOnDuplicate(
                $this->resourceConnection->getTableName(SegmentCustomerInterface::TABLE_NAME),
                $data,
                ['customer_id']
            );

            if ($affectedCount) {
                Writer::addCustomerMessage($segmentId, $affectedCount, HistoryInterface::ACTION_ADD);
            }
        }

        // Remove customers that do not match this segment anymore
        if ($remove) {
            $select = $connection->select()
                ->from($this->resourceConnection->getTableName(SegmentCustomerInterface::TABLE_NAME))
                ->where('segment_id = ?', $segmentId);

            if (!empty($data)) {
                $select->where('email NOT IN (?)', array_column($data, 'email'));
            }

            $query = $connection->deleteFromSelect(
                $select,
                $this->resourceConnection->getTableName(SegmentCustomerInterface::TABLE_NAME)
            );

            $stmt = $connection->query($query);
            Writer::addCustomerMessage($segmentId, $stmt->rowCount(), HistoryInterface::ACTION_REMOVE);
        }

        return $affectedCount + $removedCount;
    }

    /**
     * {@inheritDoc}
     */
    public function removeSegmentCustomers($segmentId)
    {
        $connection = $this->resourceConnection->getConnection();
        $query      = $connection->deleteFromSelect(
            $connection->select()
                ->from($this->resourceConnection->getTableName(SegmentCustomerInterface::TABLE_NAME))
                ->where('segment_id = ?', $segmentId),
            $this->resourceConnection->getTableName(SegmentCustomerInterface::TABLE_NAME)
        );

        $connection->query($query);
    }

    /**
     * {@inheritDoc}
     */
    public function excludeCustomersFromSegmentByIds(array $customerIds, $segmentId)
    {
        $excludeEmails = [];
        $segment       = $this->segmentRepository->get($segmentId);
        $combine       = $segment->getConditions();

        foreach ($customerIds as $customerId) {
            $customer        = $this->segmentCustomerRepository->get($customerId);
            $excludeEmails[] = $customer->getEmail(); // Collect customers' emails
            $this->segmentCustomerRepository->delete($customer); // Delete them from matched customers
        }

        /** @var \Magento\Rule\Model\Condition\AbstractCondition $condition */
        foreach ($combine->getConditions() as $condition) {
            if ($condition instanceof CustomerCondition
                && $condition->getOperator() == '!()'
            ) {
                // Add customers' emails to existent exclude condition
                $condition->setValue($condition->getValue() . ', ' . implode(',', $excludeEmails));
                $excludeEmails = []; // Reset emails
            }
        }

        if (!empty($excludeEmails)) {
            // If exclude condition does not exist yet - create it and add customers' emails
            /** @var CustomerCondition $customerCondition */
            $customerCondition = $this->conditionFactory->create('Customer');
            $customerCondition->addData([
                'type'      => CustomerCondition::class,
                'attribute' => 'email',
                'operator'  => '!()',
                'value'     => implode(',', $excludeEmails),
            ]);
            $combine->addCondition($customerCondition);
        }

        $this->segmentRepository->save($segment);

        Writer::addCustomerMessage($segmentId, count($customerIds), HistoryInterface::ACTION_REMOVE);
    }

    /**
     * {@inheritDoc}
     */
    public function changeCustomersGroup(SegmentInterface $segment)
    {
        $higherSegments = $this->getHigherSegments($segment);

        if (count($higherSegments)) { // Retrieve customers that do not belong to segments with higher priority
            $segmentCustomers = $this->segmentCustomerRepository->getCollection()
                ->addFieldToFilter('segment_id', ['in' => array_merge($higherSegments, [$segment->getId()])]);

            $segmentCustomers->getSelect()->group('customer_id')->group('segment_id')
                ->having('COUNT(segment_id) = 1')
                ->having('segment_id = ?', $segment->getId());
        } else {
            // Otherwise retrieve all customers that belong to this segment
            $segmentCustomers = $this->segmentCustomerRepository->getCollection()
                ->addFieldToFilter('segment_id', $segment->getId());
        }

        // Group can be changed only for Registered Customers
        $segmentCustomers->addFieldToFilter('customer_id', ['notnull' => true]);
        
        $this->searchCriteriaBuilder
            ->addFilter('group_id', $segment->getToGroupId(), 'neq')
            ->addFilter('entity_id', $segmentCustomers->getColumnValues('customer_id'), 'in');

        $customerList = $this->customerRepository->getList($this->searchCriteriaBuilder->create());
        /** @var CustomerInterface $customer */
        foreach ($customerList->getItems() as $customer) {
            $customer->setGroupId($segment->getToGroupId()); // Change customer group
            $this->customerRepository->save($customer);
        }

        Writer::addCustomerMessage($segment->getId(), $customerList->getTotalCount(), HistoryInterface::ACTION_GROUP);
    }

    /**
     * Retrieve segment IDs that are more prioritized than the passed one.
     *
     * @param SegmentInterface $segment
     *
     * @return array
     */
    private function getHigherSegments(SegmentInterface $segment)
    {
        $adapter = $this->resourceConnection->getConnection();
        $select  = $adapter->select()->from(
            ['segment' => $this->resourceConnection->getTableName('mst_customersegment_segment')],
            ['segment_id']
        )
            ->where('website_id = ?', $segment->getWebsiteId())
            ->where('status = 1')
            ->where("type IN(?)", SegmentInterface::TYPE_ALL . ',' . SegmentInterface::TYPE_CUSTOMER)
            ->where("priority < ? AND priority IS NOT NULL AND priority <> 0", $segment->getPriority());

        return $adapter->fetchCol($select);
    }
}
