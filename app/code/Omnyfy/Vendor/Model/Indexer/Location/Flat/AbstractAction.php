<?php

namespace Omnyfy\Vendor\Model\Indexer\Location\Flat;

abstract class AbstractAction
{

    /**
     * Suffix for value field on composite attributes
     *
     * @var string
     */
    protected $_valueFieldSuffix = '_value';

    /**
     * Suffix for drop table (uses on flat table rename)
     *
     * @var string
     */
    protected $_tableDropSuffix = '_drop_indexer';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Omnyfy\Vendor\Helper\Location\Flat\Indexer
     */
    protected $_locationIndexerHelper;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * Existing flat tables flags pool
     *
     * @var array
     */
    protected $_flatTablesExist = [];

    /**
     * @var TableBuilder
     */
    protected $_tableBuilder;

    /**
     * @var FlatTableBuilder
     */
    protected $_flatTableBuilder;

    /**
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Omnyfy\Vendor\Helper\Location\Flat\Indexer $locationHelper
     * @param TableBuilder $tableBuilder
     * @param FlatTableBuilder $flatTableBuilder
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Omnyfy\Vendor\Helper\Location\Flat\Indexer $locationHelper,
        TableBuilder $tableBuilder,
        FlatTableBuilder $flatTableBuilder
    ) {
        $this->_storeManager = $storeManager;
        $this->_locationIndexerHelper = $locationHelper;
        $this->_connection = $resource->getConnection();
        $this->_tableBuilder = $tableBuilder;
        $this->_flatTableBuilder = $flatTableBuilder;
    }

    /**
     * Execute action for given ids
     *
     * @param array|int $ids
     * @return \Omnyfy\Vendor\Model\Indexer\Location\Flat\AbstractAction
     */
    abstract public function execute($ids);

    /**
     * Return temporary table name by regular table name
     *
     * @param string $tableName
     * @return string
     */
    protected function _getTemporaryTableName($tableName)
    {
        return sprintf('%s_tmp_indexer', $tableName);
    }

    /**
     * Drop temporary tables created by reindex process
     *
     * @param array $tablesList
     * @param int|string $storeId
     * @return void
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _cleanOnFailure(array $tablesList, $storeId)
    {
        foreach ($tablesList as $table => $columns) {
            $this->_connection->dropTemporaryTable($table);
        }
        $tableName = $this->_getTemporaryTableName($this->_locationIndexerHelper->getFlatTableName($storeId));
        $this->_connection->dropTable($tableName);
    }

    /**
     * Rebuild location flat index from scratch
     *
     * @param int $storeId
     * @param array $changedIds
     * @return void
     * @throws \Exception
     */
    protected function _reindex($storeId, array $changedIds = [])
    {
        try {
            $this->_tableBuilder->build($storeId, $changedIds, $this->_valueFieldSuffix);
            $this->_flatTableBuilder->build(
                $storeId,
                $changedIds,
                $this->_valueFieldSuffix,
                $this->_tableDropSuffix,
                true
            );
        } catch (\Exception $e) {
            $attributes = $this->_locationIndexerHelper->getAttributes();
            $eavAttributes = $this->_locationIndexerHelper->getTablesStructure($attributes);
            $this->_cleanOnFailure($eavAttributes, $storeId);
            throw $e;
        }
    }

    /**
     * Check is flat table for store exists
     *
     * @param int $storeId
     * @return bool
     */
    protected function _isFlatTableExists($storeId)
    {
        if (!isset($this->_flatTablesExist[$storeId])) {
            $tableName = $this->_locationIndexerHelper->getFlatTableName($storeId);
            $isTableExists = $this->_connection->isTableExists($tableName);

            $this->_flatTablesExist[$storeId] = $isTableExists ? true : false;
        }

        return $this->_flatTablesExist[$storeId];
    }
}
