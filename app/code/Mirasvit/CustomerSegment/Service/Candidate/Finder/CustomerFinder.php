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

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Mirasvit\CustomerSegment\Api\Data\CandidateInterface;
use Mirasvit\CustomerSegment\Api\Data\CandidateInterfaceFactory;
use Mirasvit\CustomerSegment\Api\Data\Segment\StateInterface;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Api\Service\Candidate\FinderInterface;

class CustomerFinder implements FinderInterface
{
    /**
     * Finder code.
     * @var string
     */
    const CODE = 'customer';

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CandidateInterfaceFactory
     */
    private $candidateFactory;

    /**
     * CustomerFinder constructor.
     * @param CandidateInterfaceFactory $candidateFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CandidateInterfaceFactory $candidateFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerRepository    = $customerRepository;
        $this->candidateFactory      = $candidateFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Registered Customers');
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
        if ($segmentType == SegmentInterface::TYPE_CUSTOMER || $segmentType == SegmentInterface::TYPE_ALL) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function find($segmentType, $websiteId, StateInterface $state)
    {
        $candidates = [];

        if (!$this->canProcess($segmentType)) {
            return $candidates;
        }

        // filter by website
        $this->searchCriteriaBuilder->addFilter('website_id', $websiteId);

        $this->applyState($this->searchCriteriaBuilder, $state);

        $customerList = $this->customerRepository->getList($this->searchCriteriaBuilder->create());

        // save customer total size
        $state->setCustomerTotalSize($customerList->getTotalCount());

        // stop indexing customers if state size greater than size of customer collection
        if ($state->getSize() >= $customerList->getTotalCount()) {
            $state->finishStep($this->getCode());

            return $candidates;
        }

        return $this->createCandidates($customerList->getItems());
    }

    /**
     * Create candidates from customers.
     *
     * @param array       $items
     * @param StateInterface $state
     *
     * @return array
     */
    public function createCandidates(array $items = [], StateInterface $state = null)
    {
        $candidates = [];
        foreach ($items as $customer) {
            /** @var CandidateInterface $candidate */
            $candidate    = $this->candidateFactory->create();
            /** @var CustomerInterface|AbstractSimpleObject $customer */
            $candidates[] = $candidate->setData($customer->__toArray())
                ->setCustomerId($customer->getId())
                ->setName("{$customer->getFirstname()} {$customer->getLastname()}");
        }

        return $candidates;
    }

    /**
     * Apply state parameters to customer search criteria builder.
     * Limit collection according to state.
     *
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StateInterface        $state
     *
     * @return $this
     */
    private function applyState(SearchCriteriaBuilder $searchCriteriaBuilder, StateInterface $state)
    {
        $pageSize    = $state->getIndex() ? : $state->getLimit();
        $currentPage = $state->getSize() / $pageSize + 1;

        $searchCriteriaBuilder->setPageSize($pageSize);
        $searchCriteriaBuilder->setCurrentPage($currentPage);

        return $this;
    }
}
