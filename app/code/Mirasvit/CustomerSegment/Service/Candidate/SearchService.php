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



namespace Mirasvit\CustomerSegment\Service\Candidate;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Mirasvit\CustomerSegment\Api\Data\CandidateInterface;
use Mirasvit\CustomerSegment\Api\Data\CandidateInterfaceFactory;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Api\Service\Candidate\SearchResultsInterface as CandidateSearchResultsInterface;
use Mirasvit\CustomerSegment\Api\Service\Candidate\SearchResultsInterfaceFactory;
use Mirasvit\CustomerSegment\Api\Service\Candidate\SearchServiceInterface;

class SearchService implements SearchServiceInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Framework\DataObject\Copy
     */
    private $objectCopyService;

    /**
     * @var AddressInterfaceFactory
     */
    private $addressFactory;

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
     * @var CandidateInterfaceFactory
     */
    private $candidateFactory;

    /**
     * SearchService constructor.
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param CustomerInterfaceFactory $customerFactory
     * @param AddressInterfaceFactory $addressFactory
     * @param RegionInterfaceFactory $regionFactory
     * @param CandidateInterfaceFactory $candidateFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param \Magento\Framework\DataObject\Copy $objectCopyService
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        OrderCollectionFactory $orderCollectionFactory,
        CustomerInterfaceFactory $customerFactory,
        AddressInterfaceFactory $addressFactory,
        RegionInterfaceFactory $regionFactory,
        CandidateInterfaceFactory $candidateFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\DataObject\Copy $objectCopyService,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->searchCriteriaBuilder  = $searchCriteriaBuilder;
        $this->searchResultsFactory   = $searchResultsFactory;
        $this->customerRepository     = $customerRepository;
        $this->candidateFactory       = $candidateFactory;
        $this->objectCopyService      = $objectCopyService;
        $this->addressFactory         = $addressFactory;
        $this->regionFactory          = $regionFactory;
        $this->customerFactory        = $customerFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getList(SegmentInterface $segment)
    {
        \Magento\Framework\Profiler::start(__METHOD__);
        $segmentType = $segment->getType();
        $websiteId   = $segment->getWebsiteId();
        /** @var CandidateSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $excludeEmails = [];
        $candidates    = [];

        // Collect Registered Customers
        if ($segmentType == SegmentInterface::TYPE_CUSTOMER || $segmentType == SegmentInterface::TYPE_ALL) {
            $this->searchCriteriaBuilder->addFilter('website_id', $websiteId);
            $customerList = $this->customerRepository->getList($this->searchCriteriaBuilder->create());
            $searchResults->setTotalCount($searchResults->getTotalCount() + $customerList->getTotalCount());
            $customers = $customerList->getItems();
            /** @var CandidateInterface $candidate */
            /** @var CustomerInterface|AbstractSimpleObject $customer */
            foreach ($customers as $customer) {
                $candidate    = $this->candidateFactory->create();
                $candidates[] = $candidate->setData($customer->__toArray())
                    ->setCustomerId($customer->getId());
                // Collect exclude emails for later use in collect Guest Customers section
                $excludeEmails[] = $customer->getEmail();
            }
        }

        // Collect Guest Customers
        if ($segmentType == SegmentInterface::TYPE_GUEST || $segmentType == SegmentInterface::TYPE_ALL) {
            $orderCollection = $this->orderCollectionFactory->create()
                ->addFieldToFilter('customer_id', ['null' => true])
                ->addFieldToFilter('customer_email', ['notnull' => true]);
            // Filter by Website ID
            $orderCollection->getSelect()
                ->join(
                    ['store' => $orderCollection->getResource()->getTable('store')],
                    'store.store_id = main_table.store_id',
                    []
                )
                ->where('store.website_id = ?', $websiteId)
                ->group('customer_email');

            // Exclude emails of the Registered Customers collected previously
            if (!empty($excludeEmails)) {
                $orderCollection->addFieldToFilter('customer_email', ['nin' => $excludeEmails]);
            }

            foreach ($orderCollection as $order) {
                $candidate    = $this->candidateFactory->create();
                $customer     = $this->createCustomerFromOrder($order);
                $candidates[] = $candidate->setData($customer->__toArray());
            }
        }

        $searchResults->setItems($candidates);
        \Magento\Framework\Profiler::stop(__METHOD__);

        return $searchResults;
    }

    /**
     * Create customer model from order model.
     *
     * @param OrderInterface $order
     *
     * @return CustomerInterface|AbstractSimpleObject
     */
    private function createCustomerFromOrder(OrderInterface $order)
    {
        $customerData = $this->objectCopyService->copyFieldsetToTarget(
            'order_address',
            'to_customer',
            $order->getBillingAddress(),
            []
        );
        $addresses    = $order->getAddresses();
        foreach ($addresses as $address) {
            $addressData = $this->objectCopyService->copyFieldsetToTarget(
                'order_address',
                'to_customer_address',
                $address,
                []
            );
            /** @var \Magento\Customer\Api\Data\AddressInterface $customerAddress */
            $customerAddress = $this->addressFactory->create(['data' => $addressData]);
            if (is_string($address->getRegion())) {
                /** @var \Magento\Customer\Api\Data\RegionInterface $region */
                $region = $this->regionFactory->create()
                    ->setRegion($address->getRegion())
                    ->setRegionCode($address->getRegionCode())
                    ->setRegionId($address->getRegionId());
                $customerAddress->setRegion($region);
            }
            $customerData['store_id']    = $order->getStoreId(); // Assign store ID to customer data
            $customerData['addresses'][] = $customerAddress;
        }

        return $this->customerFactory->create(['data' => $customerData]);
    }
}
