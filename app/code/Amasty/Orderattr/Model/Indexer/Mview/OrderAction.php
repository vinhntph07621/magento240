<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


namespace Amasty\Orderattr\Model\Indexer\Mview;

use Magento\Framework\Mview\ActionInterface;
use Magento\Framework\Indexer\IndexerInterfaceFactory;
use Amasty\Orderattr\Model\ResourceModel\Entity\Entity;
use Amasty\Orderattr\Api\Data\CheckoutEntityInterface;

class OrderAction implements ActionInterface
{
    /**
     * @var IndexerInterfaceFactory
     */
    private $indexerFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @param IndexerInterfaceFactory $indexerFactory
     */
    public function __construct(
        IndexerInterfaceFactory $indexerFactory,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->indexerFactory = $indexerFactory;
        $this->resource = $resource;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     *
     * @return void
     * @api
     */
    public function execute($ids)
    {
        /** @var \Magento\Framework\Indexer\IndexerInterface $indexer */
        $indexer = $this->indexerFactory->create()->load(Entity::GRID_INDEXER_ID);
        $ids = $this->convertIds($ids);
        $indexer->reindexList($ids);
    }

    /**
     * Convert ParentId (Order ID) to EntityId
     *
     * @param array $ids
     *
     * @return array
     */
    protected function convertIds($ids)
    {
        $adapter = $this->resource->getConnection();
        $select = $adapter->select()
            ->from(
                $this->resource->getTableName(\Amasty\Orderattr\Setup\Operation\CreateEntityTable::TABLE_NAME),
                CheckoutEntityInterface::ENTITY_ID
            )
            ->where(CheckoutEntityInterface::PARENT_ID . ' IN (?)', $ids)
            ->where(CheckoutEntityInterface::PARENT_ENTITY_TYPE . ' = ?', CheckoutEntityInterface::ENTITY_TYPE_ORDER);

        return $adapter->fetchCol($select);
    }
}
