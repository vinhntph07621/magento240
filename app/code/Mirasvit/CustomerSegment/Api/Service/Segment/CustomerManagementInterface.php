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



namespace Mirasvit\CustomerSegment\Api\Service\Segment;


use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;

interface CustomerManagementInterface
{
    /**
     * Associate customers with segment.
     *
     * @param SegmentInterface[] $candidates
     * @param int                $segmentId
     * @param bool               $remove - should we remove old customers if they do not match this segment anymore
     *
     * @return int - number of affected rows (sum of added and removed customers)
     */
    public function addSegmentCustomers(array $candidates, $segmentId, $remove = true);

    /**
     * Remove customers associated with segment.
     *
     * @param int $segmentId
     *
     * @return void
     */
    public function removeSegmentCustomers($segmentId);

    /**
     * Exclude customers from segment, by adding their IDs to condition "Customer: Email is not one of '...'".
     *
     * @param array $customerIds IDs of customers to exclude
     * @param int   $segmentId
     *
     * @return void
     */
    public function excludeCustomersFromSegmentByIds(array $customerIds, $segmentId);

    /**
     * Move segment customers to customer group ID selected for the segment.
     * Customers' group changing occurs based on the segment priority.
     * Priority is very important because a customer can belong to multiple segments.
     * Priority of a segment determines Customer Group assigned to customer in case if he belongs to multiple segments.
     *
     * @param SegmentInterface $segment
     *
     * @return void
     */
    public function changeCustomersGroup(SegmentInterface $segment);
}