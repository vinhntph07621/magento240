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



namespace Mirasvit\CustomerSegment\Service\Segment\History;

use Mirasvit\CustomerSegment\Api\Data\Segment\HistoryInterface;
use Mirasvit\CustomerSegment\Api\Service\Segment\History\WriterInterface;

class Writer implements WriterInterface
{
    /**
     * @inheritdoc
     */
    public static function addStartMessage($segmentId)
    {
        self::addMessage($segmentId, HistoryInterface::ACTION_START);
    }

    /**
     * @param int $segmentId
     */
    public static function addStartIterationMessage($segmentId)
    {
        self::addMessage($segmentId, HistoryInterface::ACTION_START_ITERATION);
    }

    /**
     * @param int $segmentId
     * @param int $count
     */
    public static function addFinishMessage($segmentId, $count)
    {
        self::addMessage($segmentId, HistoryInterface::ACTION_FINISH, $count);
    }

    /**
     * @param int $segmentId
     * @param int $count
     */
    public static function addFinishIterationMessage($segmentId, $count)
    {
        self::addMessage($segmentId, HistoryInterface::ACTION_FINISH_ITERATION, $count);
    }

    /**
     * @param int $segmentId
     * @param int $rowsCount
     * @param string $action
     */
    public static function addCustomerMessage($segmentId, $rowsCount, $action)
    {
        self::addMessage($segmentId, $action, $rowsCount);
    }

    /**
     * Method creates new history message.
     *
     * @param int         $segmentId
     * @param string      $action
     * @param null|int    $rowsCount
     * @param null|string $message
     */
    private static function addMessage($segmentId, $action, $rowsCount = null, $message = null)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Mirasvit\CustomerSegment\Api\Data\Segment\HistoryInterface $history */
        $history = $objectManager->create('\Mirasvit\CustomerSegment\Model\Segment\History');
        /** @var \Mirasvit\CustomerSegment\Api\Repository\Segment\HistoryRepositoryInterface $historyRepository */
        $historyRepository = $objectManager->create(
            '\Mirasvit\CustomerSegment\Api\Repository\Segment\HistoryRepositoryInterface'
        );

        $history->setSegmentId($segmentId)
            ->setAction($action)
            ->setAffectedRows($rowsCount)
            ->setMessage($message)
            ->setType(php_sapi_name() == 'cli' ? 'Automatic' : 'Manual');

        $historyRepository->save($history);
    }
}
