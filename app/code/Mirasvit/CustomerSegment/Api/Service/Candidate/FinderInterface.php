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



namespace Mirasvit\CustomerSegment\Api\Service\Candidate;


use Mirasvit\CustomerSegment\Api\Data\Segment\StateInterface;

interface FinderInterface
{
    /**
     * Get finder name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get finder code.
     *
     * @return string
     */
    public function getCode();

    /**
     * Determine whether current finder is for given $segmentType.
     *
     * @param int $segmentType
     *
     * @return bool
     */
    public function canProcess($segmentType);

    /**
     * Find candidates according to finder strategy.
     *
     * @param string         $segmentType
     * @param int            $websiteId
     * @param StateInterface $state
     *
     * @return \Mirasvit\CustomerSegment\Api\Data\CandidateInterface[]
     */
    public function find($segmentType, $websiteId, StateInterface $state);

    /**
     * Create candidates from given items.
     *
     * @param string[]       $items
     * @param StateInterface $state
     *
     * @return array
     */
    public function createCandidates(array $items = [], StateInterface $state = null);
}
