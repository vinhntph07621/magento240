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




namespace Mirasvit\CsNewsletter\Api\Repository;


interface SegmentNewsletterRepositoryInterface
{
    const TABLE_NAME = 'mst_customersegment_newsletter';

    const ID         = 'cs_queue_id';
    const SEGMENT_ID = 'segment_id';
    const QUEUE_ID   = 'queue_id';

    /**
     * Get segment IDs by queue ID.
     *
     * @param int $queueId
     *
     * @return int[]
     */
    public function getByQueue($queueId);

    /**
     * Save newsletter queue - segment association.
     *
     * @param array|int $segmentIds
     * @param int       $queueId
     */
    public function save($segmentIds, $queueId);

    /**
     * Delete newsletter queue - segment association.
     *
     * @param int $queueId
     */
    public function deleteByQueue($queueId);
}
