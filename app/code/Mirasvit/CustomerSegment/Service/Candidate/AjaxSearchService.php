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

use Magento\Framework\Api\SearchResults;
use Mirasvit\CustomerSegment\Api\Data\Segment\StateInterface;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Api\Repository\Candidate\FinderRepositoryInterface;
use Mirasvit\CustomerSegment\Api\Repository\SegmentRepositoryInterface;
use Mirasvit\CustomerSegment\Api\Service\Candidate\FinderInterface;
use Mirasvit\CustomerSegment\Api\Service\Candidate\SearchResultsInterface as CandidateSearchResultsInterface;
use Mirasvit\CustomerSegment\Api\Service\Candidate\SearchResultsInterfaceFactory;
use Mirasvit\CustomerSegment\Api\Service\Candidate\SearchServiceInterface;

class AjaxSearchService implements SearchServiceInterface
{
    /**
     * @var SearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var FinderRepositoryInterface
     */
    private $finderRepository;

    /**
     * @var SegmentRepositoryInterface
     */
    private $segmentRepository;


    /**
     * AjaxSearchService constructor.
     * @param SegmentRepositoryInterface $segmentRepository
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param FinderRepositoryInterface $finderRepository
     */
    public function __construct(
        SegmentRepositoryInterface $segmentRepository,
        SearchResultsInterfaceFactory $searchResultsFactory,
        FinderRepositoryInterface $finderRepository
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->finderRepository     = $finderRepository;
        $this->segmentRepository    = $segmentRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function getList(SegmentInterface $segment)
    {
        \Magento\Framework\Profiler::start(__METHOD__);

        $candidates = [];
        /** @var CandidateSearchResultsInterface|SearchResults $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $state         = $this->segmentRepository->getState($segment);
        $finders       = $this->finderRepository->getList($segment);
        $state->setSteps($finders);

        foreach ($finders as $finder) {
            if ($this->canUse($finder, $state)) {
                $state->processStep($finder->getCode());

                $candidates = array_merge($candidates, $finder->find(
                    $segment->getType(),
                    $segment->getWebsiteId(),
                    $state
                ));

                $searchResults->setTotalCount($searchResults->getTotalCount() + count($candidates));
            }
        }

        \Magento\Framework\Profiler::stop(__METHOD__);

        return $searchResults->setItems($candidates);
    }

    /**
     * Determine whether we can use this finder or not.
     *
     * @param FinderInterface $finder
     * @param StateInterface  $state
     *
     * @return bool
     */
    private function canUse(FinderInterface $finder, StateInterface $state)
    {
        // return true - if no step exists yet or step is still processing
        if (!$state->getStep() || $state->getData($finder->getCode()) === StateInterface::STEP_STATUS_PROCESSING) {
            return true;
        }

        // return true - if previous step has been finished and current step is a new one
        if ($state->getData($state->getStep()) === StateInterface::STEP_STATUS_FINISHED
            && $state->getData($finder->getCode()) !== StateInterface::STEP_STATUS_FINISHED
            && $state->getStep() !== $finder->getCode()
        ) {
            return true;
        }

        return false;
    }
}
