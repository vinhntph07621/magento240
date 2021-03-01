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



namespace Mirasvit\CustomerSegment\Ui\Validator;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Rule\Model\Condition\Combine;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\CustomerSegment\Api\Data\CandidateInterface;
use Mirasvit\CustomerSegment\Api\Data\CandidateInterfaceFactory;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Api\Repository\Candidate\FinderRepositoryInterface;
use Mirasvit\CustomerSegment\Api\Repository\SegmentRepositoryInterface;
use Mirasvit\CustomerSegment\Service\Candidate\Finder\CustomerFinder;
use Mirasvit\CustomerSegment\Service\Candidate\Finder\GuestFinder;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var SegmentRepositoryInterface
     */
    private $segmentRepository;

    /**
     * @var string
     */
    private $customersFieldName;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CandidateInterfaceFactory
     */
    private $candidateFactory;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var FinderRepositoryInterface
     */
    private $finderRepository;

    /**
     * DataProvider constructor.
     * @param FinderRepositoryInterface $finderRepository
     * @param ResourceConnection $resource
     * @param CandidateInterfaceFactory $candidateFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param ContextInterface $context
     * @param SegmentRepositoryInterface $segmentRepository
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param string $customersFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        FinderRepositoryInterface $finderRepository,
        ResourceConnection $resource,
        CandidateInterfaceFactory $candidateFactory,
        CustomerRepositoryInterface $customerRepository,
        ContextInterface $context,
        SegmentRepositoryInterface $segmentRepository,
        $name,
        $primaryFieldName,
        $requestFieldName,
        $customersFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->resource           = $resource;
        $this->collection         = $segmentRepository->getCollection();
        $this->context            = $context;
        $this->segmentRepository  = $segmentRepository;
        $this->customerRepository = $customerRepository;
        $this->customersFieldName = $customersFieldName;
        $this->candidateFactory   = $candidateFactory;
        $this->finderRepository   = $finderRepository;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        if (!$this->context->getRequestParam($this->customersFieldName)) {
            throw new LocalizedException(__('Please enter at least 1 customer ID.'));
        }

        $segment = $this->segmentRepository->get($this->context->getRequestParam(SegmentInterface::ID));

        if (!$segment) {
            throw new LocalizedException(__('Please save segment before validation.'));
        }

        $result = [
            $segment->getId() => $segment->getData(),
        ];

        $result[$segment->getId()]['validation'] = [];

        $ids = explode(',', $this->context->getRequestParam($this->customersFieldName));

        $candidates = $this->createCandidates(array_map('trim', $ids));

        /** @var CandidateInterface|DataObject $candidate */
        foreach ($candidates as $candidate) {
            $conditions = $this->validate($candidate, $segment->getRule()->getConditions());

            $result[$segment->getId()]['validation'][] = [
                'is_valid'   => $segment->getRule()->validate($candidate),
                'conditions' => $conditions,
                'candidate'  => $candidate->getName(),
                'link'       => $candidate->getCustomerId()
                    ? $this->context->getUrl('customer/index/edit', ['id' => $candidate->getCustomerId()])
                    : false,
            ];
        }

        return $result;
    }

    /**
     * @param CandidateInterface                        $candidate
     * @param \Magento\Rule\Model\Condition\AbstractCondition $condition
     *
     * @return array
     */
    private function validate(CandidateInterface $candidate, \Magento\Rule\Model\Condition\AbstractCondition $condition)
    {
        $result = [];
        if ($condition instanceof \Mirasvit\CustomerSegment\Model\Segment\Condition\Combine) {
            foreach ($condition->getConditions() as $cond) {
                $result[] = $this->validate($candidate, $cond);
            }
        } else {
            $result = [
                'label'    => nl2br(preg_replace('/ /', '&nbsp;', $condition->asStringRecursive())),
                'is_valid' => $condition->validate($candidate),
            ];
        }

        return $result;
    }

    /**
     * @param int[] $customerIds
     *
     * @return CandidateInterface[]
     */
    private function createCandidates($customerIds)
    {
        $candidates = [];
        // create customer guests
        foreach ($customerIds as $customerId) {
            $data       = $this->getCustomer($customerId);
            $finderCode = CustomerFinder::CODE;
            if (!$data) {
                // create guest candidate
                $finderCode = GuestFinder::CODE;
                $data       = $this->getOrder($customerId);
            } else {
                $data = [$data];
            }

            $finder     = $this->finderRepository->getByCode($finderCode);
            $candidates = array_merge($candidates, $finder->createCandidates($data));
        }

        return $candidates;
    }

    /**
     * @param int|string $customerId
     *
     * @return CustomerInterface|null
     */
    private function getCustomer($customerId)
    {
        $customer = null;
        try {
            /** @var CustomerInterface|AbstractSimpleObject $customer */
            if (is_numeric($customerId)) {
                $customer = $this->customerRepository->getById($customerId);
            } elseif (filter_var($customerId, FILTER_VALIDATE_EMAIL)) {
                $customer = $this->customerRepository->get($customerId);
            }
        } catch (\Exception $e) {
        } // mute exceptions

        return $customer;
    }

    /**
     * Create guest.
     * @param string $email
     * @return array
     */
    private function getOrder($email)
    {
        return $this->resource->getConnection()->fetchAssoc($this->getGuestOrdersSelect($email));
    }

    /**
     * Retrieve guest order collection
     * @param string $email
     * @return Select
     */
    private function getGuestOrdersSelect($email)
    {
        $select = $this->resource->getConnection()->select();
        $select->from(['main_table' => $this->resource->getTableName('sales_order')], [
            OrderInterface::ENTITY_ID,
            OrderInterface::BILLING_ADDRESS_ID,
            OrderInterface::STORE_ID,
        ])
            ->where('main_table.' . OrderInterface::CUSTOMER_ID . ' IS NULL')
            ->where('main_table.' . OrderInterface::CUSTOMER_EMAIL . ' = ?', $email);

        // Group by email
        $select->group('main_table.' . OrderInterface::CUSTOMER_EMAIL);

        return $select;
    }
}
