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

use Magento\Framework\Model\AbstractModel;
use Mirasvit\CustomerSegment\Api\Data\CandidateInterface;
use Mirasvit\CustomerSegment\Api\Data\Segment\CustomerInterface;
use Mirasvit\CustomerSegment\Api\Data\Segment\HistoryInterface;
use Mirasvit\CustomerSegment\Api\Data\Segment\StateInterface;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Api\Service\Candidate\SearchServiceInterface;
use Mirasvit\CustomerSegment\Api\Service\Segment\CustomerManagementInterface as SegmentCustomerManagementInterface;
use Mirasvit\CustomerSegment\Api\Service\Segment\RuleServiceInterface;
use Mirasvit\CustomerSegment\Api\Service\SegmentServiceInterface;
use Mirasvit\CustomerSegment\Model\Config;
use Mirasvit\CustomerSegment\Repository\SegmentRepository;
use Mirasvit\CustomerSegment\Service\Candidate\Finder\GuestFinder;
use Mirasvit\CustomerSegment\Service\Segment\History\Writer;

class AjaxRuleService implements RuleServiceInterface
{
    /**
     * Max limit for script execution time.
     * @var int
     */
    const MAX_TIME_LIMIT = 60;

    /**
     * Default limit for script execution time.
     * @var int
     */
    const DEFAULT_TIME_LIMIT = 30;

    /**
     * Max limit for amount of processed candidates per request.
     * @var int
     */
    const MAX_LIMIT = 500;

    /**
     * Default limit for amount of processed candidates per request.
     * @var int
     */
    const LIMIT = 300;

    /**
     * Min percent of candidates processed per request.
     * @var int
     */
    const MIN_PERCENT = 3;

    /**
     * @var SearchServiceInterface
     */
    private $candidateSearchService;

    /**
     * @var SegmentCustomerManagementInterface
     */
    private $customerManagement;

    /**
     * @var SegmentServiceInterface
     */
    private $segmentService;

    /**
     * @var Exporter
     */
    private $exporter;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var SegmentRepository
     */
    private $segmentRepository;


    /**
     * AjaxRuleService constructor.
     * @param Config $config
     * @param SegmentRepository $segmentRepository
     * @param Exporter $exporter
     * @param SegmentServiceInterface $segmentService
     * @param SearchServiceInterface $candidateSearchService
     * @param SegmentCustomerManagementInterface $customerManagement
     */
    public function __construct(
        Config $config,
        SegmentRepository $segmentRepository,
        Exporter $exporter,
        SegmentServiceInterface $segmentService,
        SearchServiceInterface $candidateSearchService,
        SegmentCustomerManagementInterface $customerManagement
    ) {
        $this->segmentRepository      = $segmentRepository;
        $this->candidateSearchService = $candidateSearchService;
        $this->customerManagement     = $customerManagement;
        $this->segmentService         = $segmentService;
        $this->exporter               = $exporter;
        $this->config                 = $config;
    }

    /**
     * @param SegmentInterface $segment
     * @return StateInterface
     */
    public function getState(SegmentInterface $segment)
    {
        return $this->segmentRepository->getState($segment);
    }

    /**
     * {@inheritdoc}
     */
    public function apply(SegmentInterface $segment)
    {
        $this->start($segment);

        // Get list of all possible candidates for segment
        $candidateList = $this->candidateSearchService->getList($segment);

        // Filter candidates to find segment customers
        $matchedCandidates = $this->processRules($segment, $candidateList->getItems());

        // Persist segment customers
        $affectedRowsCount = $this->customerManagement->addSegmentCustomers(
            $matchedCandidates,
            $segment->getId(),
            false
        );

        $this->finish($segment, $affectedRowsCount);

        return count($matchedCandidates);
    }

    /**
     * Apply segment rules to specific type of customers(registered or not)
     *
     * @param SegmentInterface                                        $segment
     * @param \Mirasvit\CustomerSegment\Api\Data\CandidateInterface[] $candidates
     *
     * @return array
     */
    protected function processRules(SegmentInterface $segment, array $candidates)
    {
        $index               = 1;
        $processedCandidates = [];

        $state = $this->getState($segment);
        $state->setRuleStartTime(microtime(true));

        /** @var CandidateInterface|AbstractModel $candidate */
        foreach ($candidates as $candidate) {
            if ($segment->getRule()->validate($candidate)) {
                $candidate->setData([
                    SegmentInterface::ID                  => $segment->getId(),
                    CustomerInterface::CUSTOMER_ID        => $candidate->getCustomerId(),
                    CustomerInterface::EMAIL              => $candidate->getEmail(),
                    CustomerInterface::BILLING_ADDRESS_ID => $candidate->getBillingAddressId(),
                    CustomerInterface::CREATED_AT         => (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT),
                ]);

                $processedCandidates[] = $candidate;
            }

            $state->setIndex($index++); // update state index

            if ($this->isTimeout($state)) {
                $state->setLimit($this->getLimit($state));
                $state->setData('timed_out', 1);
                $state->setData('all_candidates_count', count($candidates));
                // update last order id on interrupted candidate
                if ($candidate->getOrderId()) {
                    $state->setData(GuestFinder::LAST_ORDER_ID, $candidate->getOrderId());
                }

                break;
            }
        }

        if ($index < self::LIMIT && !$state->hasData('timed_out')) {
            $state->setLimit($this->getLimit($state));
        }

        $state->setMatchedCandidatesCount(count($processedCandidates));

        return $processedCandidates;
    }

    /**
     * Initialize start of refreshing segment data, log necessary info.
     *
     * @param SegmentInterface $segment
     *
     * @return void
     */
    private function start(SegmentInterface $segment)
    {
        $state = $this->getState($segment);
        // init start timer
        $state->setStartedAt(microtime(true));

        // remove all segment customers
        if ($state->getStatus() === StateInterface::STATUS_NEW) {
            Writer::addStartMessage($segment->getId());
            $segmentCustomersCount = $this->segmentService->getCustomersCount($segment->getId());
            $this->customerManagement->removeSegmentCustomers($segment->getId());
            Writer::addCustomerMessage($segment->getId(), $segmentCustomersCount, HistoryInterface::ACTION_REMOVE);
        } else {
            Writer::addStartIterationMessage($segment->getId());
        }
    }

    /**
     * Finish refreshing segment data in this iteration.
     *
     * @param SegmentInterface $segment
     * @param int              $affectedRowsCount
     *
     * @return void
     */
    private function finish(SegmentInterface $segment, $affectedRowsCount)
    {
        $state = $this->getState($segment);
        // re-save refreshed size of candidates
        $state->setSize($state->getSize() + $state->getIndex());

        // set status to "complete" when all candidates processed
        if ($state->isFinished()) {
            $totalSize = $this->segmentService->getCustomersCount($segment->getId());
            $state->setStatus(StateInterface::STATUS_COMPLETED);
            $state->setTotalSize($totalSize);
            Writer::addFinishMessage($segment->getId(), $totalSize);

            // update segment updated_at time to correspond to refresh finished time
            $segment->setUpdatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
            $segment->save();

            if ($this->config->isExportAllowed()) {
                // publish 'segment refreshed' message to export segment customers
                $this->exporter->export($segment->getId());
            }
        } else {
            $state->setStatus(StateInterface::STATUS_CONTINUE);
            Writer::addFinishIterationMessage($segment->getId(), $affectedRowsCount);
        }

        // Change customers' group
        if ($segment->getToGroupId()) {
            $this->customerManagement->changeCustomersGroup($segment);
        }
    }

    /**
     * Is timeout?
     *
     * @param StateInterface $state
     *
     * @return bool
     */
    public function isTimeout(StateInterface $state)
    {
        $isTimeout = microtime(true) - $state->getStartedAt() > $this->getMaxAllowedTime($state);

        return $isTimeout;
    }

    /**
     * Check and return maximum allowed script execution time
     *
     * @param StateInterface $state
     *
     * @return int
     */
    public function getMaxAllowedTime(StateInterface $state)
    {
        $time = (int)ini_get('max_execution_time');

        if ($time < 1 || $time > self::DEFAULT_TIME_LIMIT) {
            if ($state->getStepPercent() < self::MIN_PERCENT && $time > self::MAX_TIME_LIMIT) {
                $time = self::MAX_TIME_LIMIT;
            } else {
                $time = self::DEFAULT_TIME_LIMIT;
            }
        }

        return $time;
    }

    /**
     * @param StateInterface $state
     *
     * @return int
     */
    private function getLimit(StateInterface $state)
    {
        $size = $state->getIndex();
        // if less than one percent use the default limit size
        if ($state->getStepPercent() < self::MIN_PERCENT) {
            $sizePerSec = $state->getIndex() / (microtime(true) - $state->getRuleStartTime());
            $size       = ceil($sizePerSec * ($this->getMaxAllowedTime($state) - ($state->getRuleStartTime() - $state->getStartedAt())));
            if ($size > self::MAX_LIMIT) {
                $size = self::MAX_LIMIT;
            } elseif ($size <= 0) {
                $size = self::LIMIT;
            }
        }

        return $size;
    }
}
