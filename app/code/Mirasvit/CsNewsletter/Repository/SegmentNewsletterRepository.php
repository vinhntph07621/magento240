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




namespace Mirasvit\CsNewsletter\Repository;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Mirasvit\CsNewsletter\Api\Repository\SegmentNewsletterRepositoryInterface;

class SegmentNewsletterRepository implements SegmentNewsletterRepositoryInterface
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * SegmentNewsletterRepository constructor.
     * @param ResourceConnection $resource
     */
    public function __construct(ResourceConnection $resource)
    {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
    }

    /**
     * @inheritdoc
     */
    public function getByQueue($queueId)
    {
        $select = $this->connection->select();
        $select->from($this->resource->getTableName(self::TABLE_NAME), self::SEGMENT_ID)
            ->where(self::QUEUE_ID . ' = ?', $queueId);

        return $this->connection->fetchCol($select, self::SEGMENT_ID);
    }

    /**
     * @inheritdoc
     */
    public function save($segmentIds, $queueId)
    {
        // first delete old values
        $this->deleteByQueue($queueId);

        // collect data to insert
        $data = [];
        foreach ($segmentIds as $id) {
            $data[] = [self::SEGMENT_ID => $id, self::QUEUE_ID => $queueId];
        }

        // insert data
        $this->connection->insertMultiple($this->resource->getTableName(self::TABLE_NAME), $data);
    }

    /**
     * @inheritdoc
     */
    public function deleteByQueue($queueId)
    {
        $this->connection->delete(
            $this->resource->getTableName(self::TABLE_NAME),
            [self::QUEUE_ID . ' = ?' => $queueId]
        );
    }
}
