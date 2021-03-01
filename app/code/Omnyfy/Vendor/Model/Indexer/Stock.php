<?php
/**
 * Project: apcd.
 * User: jing
 * Date: 10/9/18
 * Time: 2:45 PM
 */
namespace Omnyfy\Vendor\Model\Indexer;

use Magento\Framework\Indexer\CacheContext;

class Stock implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    protected $_resource;

    protected $_connection;

    protected $_isNeedUseIdxTable = false;

    protected $cacheContext;

    protected $eventManager;

    protected $_stockIndexer;

    protected $_skipTypes = [
        \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE
    ];

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Omnyfy\Vendor\Model\Resource\Indexer\Stock $stockIndexer,
        \Magento\Framework\Indexer\CacheContext $cacheContext,
        \Magento\Framework\Event\ManagerInterface $eventManager
    )
    {
        $this->_resource = $resource;
        $this->_stockIndexer = $stockIndexer;
        $this->cacheContext = $cacheContext;
        $this->eventManager = $eventManager;
    }

    public function execute($ids)
    {
        if (empty($ids)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Could not rebuild index for empty products array')
            );
        }
        try {
            $this->_reindexRows($ids);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }

        $this->getCacheContext()->registerEntities(\Omnyfy\Vendor\Model\Inventory::CACHE_TAG, $ids);
    }

    public function executeList(array $ids)
    {
        if (empty($ids)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Could not rebuild index for empty products array')
            );
        }
        try {
            $this->_reindexRows($ids);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }
    }

    public function executeFull()
    {
        try {
            $this->reindexAll();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }
        $this->getCacheContext()->registerTags(
            [
                \Omnyfy\Vendor\Model\Inventory::CACHE_TAG
            ]
        );
    }

    public function executeRow($id)
    {
        if (!isset($id) || empty($id)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We can\'t rebuild the index for an undefined product.')
            );
        }
        try {
            $this->_reindexRows([$id]);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }
    }

    protected function getCacheContext()
    {
        if (!($this->cacheContext instanceof CacheContext)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(CacheContext::class);
        } else {
            return $this->cacheContext;
        }
    }

    protected function _getConnection()
    {
        if (null === $this->_connection) {
            $this->_connection = $this->_resource->getConnection();
        }
        return $this->_connection;
    }

    protected function _getTable($entityName)
    {
        return $this->_resource->getTableName($entityName);
    }

    protected function _reindexAll()
    {
        $this->useIdxTable(true);
        $this->clearTemporaryIndexTable();

        $this->_stockIndexer->reindexAll();

        $this->_syncData();
    }

    protected function _deleteOldRelations($tableName) {
        $select = $this->_getConnection()->select()
            ->from(['s' => $tableName])
            ->where('');

        $sql = $select->deleteFromSelect('s');
        $this->_getConnection()->query($sql);
    }

    protected function _reindexRows($productIds = [])
    {
        $connection = $this->_getConnection();
        if (!is_array($productIds)) {
            $productIds = [$productIds];
        }
        $parentIds = $this->getRelationsByChild($productIds);
        $processIds = $parentIds ? array_merge($parentIds, $productIds) : $productIds;

        // retrieve product types by processIds
        $select = $connection->select()
            ->from($this->_getTable('catalog_product_entity'), ['entity_id', 'type_id'])
            ->where('entity_id IN(?)', $processIds);
        $pairs = $connection->fetchPairs($select);

        $byType = [];
        foreach ($pairs as $productId => $typeId) {
            $byType[$typeId][$productId] = $productId;
        }

        foreach($byType as $typeId => $pIds) {
            if (in_array($typeId, $this->_skipTypes)) {
                continue;
            }
            $this->_stockIndexer->reindexEntity($pIds);
        }

        $this->cacheContext->registerEntities(\Magento\Catalog\Model\Product::CACHE_TAG, $processIds);
        $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this->cacheContext]);

        return $this;
    }

    protected function _syncData()
    {
        $idxTableName = $this->_getIdxTable();
        $tableName = $this->_getTable('omnyfy_vendor_inventory_item');

        $this->_deleteOldRelations($tableName);

        $columns = array_keys($this->_connection->describeTable($idxTableName));
        $select = $this->_connection->select()->from($idxTableName, $columns);
        $query = $select->insertFromSelect($tableName, $columns);
        $this->_connection->query($query);
        return $this;
    }

    public function useIdxTable($value=null)
    {
        if ($value !== null) {
            $this->_isNeedUseIdxTable = (bool)$value;
        }
        return $this->_isNeedUseIdxTable;
    }

    protected function _getIdxTable()
    {
        if ($this->useIdxTable()) {
            return $this->_getTable('omnyfy_vendor_inventory_item_idx');
        }
        return $this->_getTable('omnyfy_vendor_inventory_item_tmp');
    }

    public function clearTemporaryIndexTable()
    {
        $this->_getConnection()->delete($this->_getIdxTable());
    }
}