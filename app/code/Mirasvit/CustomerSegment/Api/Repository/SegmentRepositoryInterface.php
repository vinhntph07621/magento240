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



namespace Mirasvit\CustomerSegment\Api\Repository;

use Mirasvit\CustomerSegment\Api\Data\Segment\StateInterface;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;

interface SegmentRepositoryInterface
{
    /**
     * @param int $id
     *
     * @return SegmentInterface|false
     */
    public function get($id);

    /**
     * @param SegmentInterface $segment
     *
     * @return SegmentInterface
     */
    public function save(SegmentInterface $segment);

    /**
     * @param SegmentInterface $segment
     *
     * @return bool
     */
    public function delete(SegmentInterface $segment);

    /**
     * @return \Mirasvit\CustomerSegment\Model\ResourceModel\Segment\Collection|SegmentInterface[]
     */
    public function getCollection();

    /**
     * @return SegmentInterface
     */
    public function create();

    /**
     * @param SegmentInterface $segment
     *
     * @return StateInterface
     */
    public function getState(SegmentInterface $segment);
}