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



namespace Mirasvit\CustomerSegment\Service\Candidate\Finder;

use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\AddressFactory as OrderAddressFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Mirasvit\CustomerSegment\Api\Data\CandidateInterfaceFactory;
use Mirasvit\CustomerSegment\Api\Data\Segment\CustomerInterface as SegmentCustomerInterface;
use Mirasvit\CustomerSegment\Api\Data\Segment\StateInterface;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Api\Service\Candidate\FinderInterface;

class GuestFinder implements FinderInterface
{
    /**
     * Finder code.
     * @var string
     */
    const CODE = 'guest';

    /**
     * Key used in state object to point to the last refreshed order.
     */
    const LAST_ORDER_ID = 'last_order_id';

    /**
     * @var \Magento\Framework\DataObject\Copy
     */
    private $objectCopyService;

    /**
     * @var AddressInterfaceFactory
     */
    private $customerAddressFactory;

    /**
     * @var RegionInterfaceFactory
     */
    private $regionFactory;

    /**
     * @var CustomerInterfaceFactory
     */
    private $customerFactory;

    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var OrderAddressFactory
     */
    private $orderAddressFactory;
    /**
     * @var CandidateInterfaceFactory
     */
    private $candidateFactory;

    /**
     * GuestFinder constructor.
     * @param OrderAddressFactory $orderAddressFactory
     * @param ResourceConnection $resource
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param CustomerInterfaceFactory $customerFactory
     * @param AddressInterfaceFactory $customerAddressFactory
     * @param RegionInterfaceFactory $regionFactory
     * @param CandidateInterfaceFactory $candidateFactory
     * @param \Magento\Framework\DataObject\Copy $objectCopyService
     */
    public function __construct(
        OrderAddressFactory $orderAddressFactory,
        ResourceConnection $resource,
        OrderCollectionFactory $orderCollectionFactory,
        CustomerInterfaceFactory $customerFactory,
        AddressInterfaceFactory $customerAddressFactory,
        RegionInterfaceFactory $regionFactory,
        CandidateInterfaceFactory $candidateFactory,
        \Magento\Framework\DataObject\Copy $objectCopyService
    ) {
        $this->candidateFactory       = $candidateFactory;
        $this->objectCopyService      = $objectCopyService;
        $this->customerAddressFactory = $customerAddressFactory;
        $this->regionFactory          = $regionFactory;
        $this->customerFactory        = $customerFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->resource               = $resource;
        $this->orderAddressFactory    = $orderAddressFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Guest Customers');
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return self::CODE;
    }

    /**
     * Can process or not.
     *
     * @param int $segmentType
     *
     * @return bool
     */
    public function canProcess($segmentType)
    {
        if ($segmentType == SegmentInterface::TYPE_GUEST || $segmentType == SegmentInterface::TYPE_ALL) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function find($segmentType, $websiteId, StateInterface $state)
    {
        if (!$this->canProcess($segmentType)) {
            return [];
        }

        $select = $this->getGuestOrdersSelect();
        $this->addFilterByWebsite($select, $websiteId)
            ->excludeSegmentCustomers($select);

        // limit collection according to state
        $this->applyState($select, $state);

        // log orders retrieve process
        $start = microtime(true);

        $orders = $this->resource->getConnection()->fetchAssoc($select->__toString());

        $state->setRetrieveOrderCollectionTime(round(microtime(true) - $start, 4));
        $state->setSelect($select->__toString());

        $start      = microtime(true);
        $candidates = $this->createCandidates($orders, $state);
        $state->setData('guest_creation', round(microtime(true) - $start, 4));

        return $candidates;
    }

    /**
     * Create customer model from billing address.
     *
     * @param int $addressId
     * @param int $storeId
     *
     * @return CustomerInterface|AbstractSimpleObject
     */
    private function createCustomerFromOrder($addressId, $storeId)
    {
        /** @var \Magento\Sales\Model\Order\Address $address */
        $address      = $this->orderAddressFactory->create()->load($addressId);
        $customerData = $this->objectCopyService->copyFieldsetToTarget(
            'order_address',
            'to_customer',
            $address,
            []
        );

        $customerData['store_id'] = $storeId; // Assign store ID to customer data

        // temporary comment out address data copy for guest customers
        //$addresses = $order->getAddresses();
        //foreach ($addresses as $address) {
        //    $addressData = $this->objectCopyService->copyFieldsetToTarget(
        //        'order_address',
        //        'to_customer_address',
        //        $address,
        //        []
        //    );
        //
        //    /** @var \Magento\Customer\Api\Data\AddressInterface $customerAddress */
        //    $customerAddress = $this->customerAddressFactory->create(['data' => $addressData]);
        //    if (is_string($address->getRegion())) {
        //        /** @var \Magento\Customer\Api\Data\RegionInterface $region */
        //        $region = $this->regionFactory->create()
        //            ->setRegion($address->getRegion())
        //            ->setRegionCode($address->getRegionCode())
        //            ->setRegionId($address->getRegionId());
        //        $customerAddress->setRegion($region);
        //    }
        //    $customerData['addresses'][] = $customerAddress;
        //}

        return $this->customerFactory->create(['data' => $customerData]);
    }

    /**
     * Retrieve guest order collection
     * @return Select
     */
    private function getGuestOrdersSelect()
    {
        $select = $this->resource->getConnection()->select();
        $select->from(['main_table' => $this->resource->getTableName('sales_order')], [
            OrderInterface::ENTITY_ID,
            OrderInterface::BILLING_ADDRESS_ID,
            OrderInterface::STORE_ID,
        ])
            ->where('main_table.' . OrderInterface::CUSTOMER_ID . ' IS NULL')
            ->where('main_table.' . OrderInterface::CUSTOMER_EMAIL . ' IS NOT NULL')
            ->order('main_table.' . OrderInterface::ENTITY_ID . ' ASC');

        // Group by email
        $select->group('main_table.' . OrderInterface::BILLING_ADDRESS_ID);

        return $select;
    }

    /**
     * Filter order collection by website ID.
     *
     * @param Select $select
     * @param int    $websiteId
     *
     * @return $this
     */
    private function addFilterByWebsite(Select $select, $websiteId)
    {
        $select
            ->join(
                ['store' => $this->resource->getTableName('store')],
                'store.store_id = main_table.store_id',
                []
            )
            ->where('store.website_id = ?', $websiteId);

        return $this;
    }

    /**
     * Exclude emails of the Registered Customers collected previously.
     *
     * @param Select $select
     *
     * @return $this
     */
    private function excludeSegmentCustomers(Select $select)
    {
        $select
            ->joinLeft(
                ['segment_customer' => $this->resource->getTableName(SegmentCustomerInterface::TABLE_NAME)],
                'main_table.customer_email = segment_customer.email AND segment_customer.customer_id IS NOT NULL',
                []
            )
            ->where('segment_customer.email IS NULL');

        return $this;
    }

    /**
     * Apply state parameters to guest collection.
     *
     * @param Select         $select
     * @param StateInterface $state
     *
     * @return $this
     */
    private function applyState(Select $select, StateInterface $state)
    {
        if ($state->getGuestTotalSize() === null) {
            // save guest size
            $countSelect = clone $select;
            $countSelect->reset(\Magento\Framework\DB\Select::ORDER);
            $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
            $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
            $countSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
            $countSelect->reset(\Magento\Framework\DB\Select::GROUP);

            $group = $select->getPart(\Magento\Framework\DB\Select::GROUP);
            $countSelect->columns(new \Zend_Db_Expr(("COUNT(DISTINCT " . implode(", ", $group) . ")")));

            $guestSize = $this->resource->getConnection()->fetchOne($countSelect);

            $state->setGuestTotalSize($guestSize);
        }

        // update state step status
        if ($state->getSize() >= $state->getGuestTotalSize()) {
            $state->finishStep($this->getCode());
        }

        // limit collection
        $select->limit($state->getLimit(), 0);

        if ($state->getData(self::LAST_ORDER_ID)) {
            $select->where('main_table.entity_id > ?', $state->getData(self::LAST_ORDER_ID));
        }

        return $this;
    }

    /**
     * Create candidates from orders.
     *
     * @param mixed[]       $items
     * @param StateInterface $state
     *
     * @return array
     */
    public function createCandidates(array $items = [], StateInterface $state = null)
    {
        $candidates = [];
        foreach ($items as $order) {
            $start        = microtime(true);
            $candidate    = $this->candidateFactory->create();
            $customer     = $this->createCustomerFromOrder($order[OrderInterface::BILLING_ADDRESS_ID], $order[OrderInterface::STORE_ID]);
            $candidates[] = $candidate->setData($customer->__toArray())
                ->setOrderId($order[OrderInterface::ENTITY_ID])
                ->setBillingAddressId($order[OrderInterface::BILLING_ADDRESS_ID])
                ->setName("{$customer->getFirstname()} {$customer->getLastname()}");

            if ($state) {
                $state->setData(self::LAST_ORDER_ID, $order[OrderInterface::ENTITY_ID]); // save last order id
                $time = round(microtime(true) - $start, 4);
                if ($time > $state->getData('createCustomerFromOrder')) {
                    $state->setData('createCustomerFromOrder', $time);
                }
            }
        }

        return $candidates;
    }
}
