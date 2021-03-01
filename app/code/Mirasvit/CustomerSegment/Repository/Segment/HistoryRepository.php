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



namespace Mirasvit\CustomerSegment\Repository\Segment;

use Magento\Framework\Exception\NoSuchEntityException;
use Mirasvit\CustomerSegment\Api\Data\Segment\HistoryInterface;
use Mirasvit\CustomerSegment\Model\Segment\HistoryFactory;
use Mirasvit\CustomerSegment\Model\ResourceModel\Segment\History\CollectionFactory;
use Mirasvit\CustomerSegment\Api\Repository\Segment\HistoryRepositoryInterface;

class HistoryRepository implements HistoryRepositoryInterface
{
    /**
     * @var HistoryInterface[]
     */
    private $historyRegistry = [];
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var HistoryFactory
     */
    private $historyFactory;

    /**
     * HistoryRepository constructor.
     *
     * @param HistoryFactory                       $historyFactory
     * @param CollectionFactory                    $collectionFactory
     */
    public function __construct(
        HistoryFactory $historyFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->historyFactory = $historyFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        if (isset($this->historyRegistry[$id])) {
            return $this->historyRegistry[$id];
        }

        /** @var HistoryInterface $history */
        $history = $this->historyFactory->create()->load($id);
        if (!$history->getId()) {
            throw NoSuchEntityException::singleField('history_id', $id);
        }

        return $history;
    }

    /**
     * {@inheritDoc}
     */
    public function save(HistoryInterface $history)
    {
        /** @var HistoryInterface $history */
        $dateTime = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        if (!$history->getId()) {
            $history->setCreatedAt($dateTime);
        }

        return $history->save();
    }

    /**
     * {@inheritDoc}
     */
    public function delete(HistoryInterface $history)
    {
        return $history->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }
}
