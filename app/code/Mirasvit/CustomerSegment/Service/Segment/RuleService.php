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
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Api\Service\Candidate\SearchServiceInterface;
use Mirasvit\CustomerSegment\Api\Service\Segment\CustomerManagementInterface as SegmentCustomerManagementInterface;
use Mirasvit\CustomerSegment\Api\Service\Segment\RuleServiceInterface;
use Mirasvit\CustomerSegment\Service\Segment\History\Writer;

class RuleService implements RuleServiceInterface
{
    /**
     * @var SearchServiceInterface
     */
    private $candidateSearchService;

    /**
     * @var SegmentCustomerManagementInterface
     */
    private $customerManagement;

    /**
     * RuleService constructor.
     * @param SearchServiceInterface $candidateSearchService
     * @param SegmentCustomerManagementInterface $customerManagement
     */
    public function __construct(
        SearchServiceInterface $candidateSearchService,
        SegmentCustomerManagementInterface $customerManagement
    ) {
        $this->candidateSearchService = $candidateSearchService;
        $this->customerManagement     = $customerManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(SegmentInterface $segment)
    {
        Writer::addStartMessage($segment->getId());

        // Get list of candidates for segment
        $candidateList = $this->candidateSearchService->getList($segment);

        // Filter candidates to find segment customers
        $matchedCandidates = $this->processRules($segment, $candidateList->getItems());

        // Persist segment customers
        $affectedRowsCount = $this->customerManagement->addSegmentCustomers($matchedCandidates, $segment->getId());

        if ($segment->getToGroupId()) {
            // Change customers' group
            $this->customerManagement->changeCustomersGroup($segment);
        }

        Writer::addFinishMessage($segment->getId(), $affectedRowsCount);

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
        \Magento\Framework\Profiler::start(__METHOD__);
        /** @var CandidateInterface|AbstractModel $candidate */
        foreach ($candidates as $candidateIdx => $candidate) {
            if ($segment->getRule()->validate($candidate)) {
                $candidate->setData([
                    'segment_id'  => $segment->getId(),
                    'customer_id' => $candidate->getCustomerId(),
                    'email'       => $candidate->getEmail(),
                    'created_at'  => (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT),
                ]);
            } else {
                unset($candidates[$candidateIdx]);
            }
        }
        \Magento\Framework\Profiler::stop(__METHOD__);

        return $candidates;
    }
}
