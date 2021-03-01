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



namespace Mirasvit\CustomerSegment\Api\Service\Segment\History;


interface WriterInterface
{
    /**
     * Add message indicated start of refreshing segment data.
     *
     * @param int $segmentId
     */
    public static function addStartMessage($segmentId);

    /**
     * Add message indicated finish of refreshing segment data.
     *
     * @param int $segmentId
     * @param int $count - count of affected rows
     */
    public static function addFinishMessage($segmentId, $count);

    /**
     * Add message indicated about number of affected rows (added or removed customers).
     *
     * @param int  $segmentId
     * @param int  $rowsCount - count of affected rows
     * @param bool $action    - specify action type: ACTION_ADD|ACTION_REMOVE|ACTION_GROUP
     */
    public static function addCustomerMessage($segmentId, $rowsCount, $action);
}