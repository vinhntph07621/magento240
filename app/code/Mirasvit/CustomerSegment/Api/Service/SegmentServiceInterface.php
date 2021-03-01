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



namespace Mirasvit\CustomerSegment\Api\Service;


use Mirasvit\CustomerSegment\Model\ResourceModel\Segment\Customer\Collection;

interface SegmentServiceInterface
{
    /**
     * Get number of customers matched to segment.
     *
     * @param int $segmentId
     *
     * @return int
     */
    public function getCustomersCount($segmentId);

    /**
     * @param int $segmentAId
     * @param int $segmentBId
     *
     * @return int
     */
    public function getCustomersInterceptionCount($segmentAId, $segmentBId);

    /**
     * Get customers collection matched to segment.
     *
     * @param int $segmentId
     *
     * @return Collection
     */
    public function getCustomers($segmentId);
}