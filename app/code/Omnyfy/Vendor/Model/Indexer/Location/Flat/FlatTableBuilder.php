<?php

namespace Omnyfy\Vendor\Model\Indexer\Location\Flat;

class FlatTableBuilder
{

    const MAX_INDEX_COUNT = 64;

    /**
     * @var \Omnyfy\Vendor\Helper\Location\Flat\Indexer
     */
    protected $_locationIndexerHelper;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $config
     */
    protected $_config;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var TableDataInterface
     */
    protected $_tableData;

    /**
     * @param \Omnyfy\Vendor\Helper\Location\Flat\Indexer $locationIndexerHelper
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param TableDataInterface $tableData
     */
    public function __construct(
        \Omnyfy\Vendor\Helper\Location\Flat\Indexer $locationIndexerHelper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Omnyfy\Vendor\Model\Indexer\Location\Flat\TableDataInterface $tableData
    ) {
        $this->_locationIndexerHelper = $locationIndexerHelper;
        $this->_connection = $resource->getConnection();
        $this->_config = $config;
        $this->_storeManager = $storeManager;
        $this->_tableData = $tableData;
    }

    /**
     * Prepare temporary flat tables
     *
     * @param int|string $storeId
     * @param array $changedIds
     * @param string $valueFieldSuffix
     * @param string $tableDropSuffix
     * @param bool $fillTmpTables
     * @return void
     */
    public function build($storeId, $changedIds, $valueFieldSuffix, $tableDropSuffix, $fillTmpTables)
    {
        $attributes = $this->_locationIndexerHelper->getAttributes();
        $eavAttributes = $this->_locationIndexerHelper->getTablesStructure($attributes);

        $this->_createTemporaryFlatTable($storeId);

        if ($fillTmpTables) {
            $this->_fillTemporaryFlatTable($eavAttributes, $storeId, $valueFieldSuffix);
            //Update zero based attributes by values from current store
            $this->_updateTemporaryTableByStoreValues($eavAttributes, $changedIds, $storeId, $valueFieldSuffix);
        }

        $flatTable = $this->_locationIndexerHelper->getFlatTableName($storeId);
        $flatDropName = $flatTable . $tableDropSuffix;
        $temporaryFlatTableName = $this->_getTemporaryTableName(
            $this->_locationIndexerHelper->getFlatTableName($storeId)
        );
        $this->_tableData->move($flatTable, $flatDropName, $temporaryFlatTableName);
    }

    /**
     * Prepare flat table for store
     *
     * @param int|string $storeId
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _createTemporaryFlatTable($storeId)
    {
        $columns = $this->_locationIndexerHelper->getFlatColumns();

        $indexesNeed = $this->_locationIndexerHelper->getFlatIndexes();

        $maxIndex = self::MAX_INDEX_COUNT;
        if ($maxIndex && count($indexesNeed) > $maxIndex) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'The module has a limit of %2$d filterable and/or sortable attributes.'
                    . 'Currently there are %1$d of them.'
                    . 'Please reduce the number of filterable/sortable attributes in order to use this module',
                    count($indexesNeed),
                    $maxIndex
                )
            );
        }

        $indexKeys = [];
        $indexProps = array_values($indexesNeed);
        $upperPrimaryKey = strtoupper(\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY);
        foreach ($indexProps as $i => $indexProp) {
            $indexName = $this->_connection->getIndexName(
                $this->_getTemporaryTableName($this->_locationIndexerHelper->getFlatTableName($storeId)),
                $indexProp['fields'],
                $indexProp['type']
            );
            $indexProp['type'] = strtoupper($indexProp['type']);
            if ($indexProp['type'] == $upperPrimaryKey) {
                $indexKey = $upperPrimaryKey;
            } else {
                $indexKey = $indexName;
            }

            $indexProps[$i] = [
                'KEY_NAME' => $indexName,
                'COLUMNS_LIST' => $indexProp['fields'],
                'INDEX_TYPE' => strtolower($indexProp['type']),
            ];
            $indexKeys[$i] = $indexKey;
        }
        $indexesNeed = array_combine($indexKeys, $indexProps);

        /** @var $table \Magento\Framework\DB\Ddl\Table */
        $table = $this->_connection->newTable(
            $this->_getTemporaryTableName($this->_locationIndexerHelper->getFlatTableName($storeId))
        );
        foreach ($columns as $fieldName => $fieldProp) {
            $columnLength = isset($fieldProp['length']) ? $fieldProp['length'] : null;

            $columnDefinition = [
                'nullable' => isset($fieldProp['nullable']) ? (bool)$fieldProp['nullable'] : false,
                'unsigned' => isset($fieldProp['unsigned']) ? (bool)$fieldProp['unsigned'] : false,
                'default' => isset($fieldProp['default']) ? $fieldProp['default'] : false,
                'primary' => false,
            ];

            $columnComment = isset($fieldProp['comment']) ? $fieldProp['comment'] : $fieldName;

            $table->addColumn($fieldName, $fieldProp['type'], $columnLength, $columnDefinition, $columnComment);
        }

        foreach ($indexesNeed as $indexProp) {
            $table->addIndex(
                $indexProp['KEY_NAME'],
                $indexProp['COLUMNS_LIST'],
                ['type' => $indexProp['INDEX_TYPE']]
            );
        }

        $table->setComment("Vendor Location Flat (Store {$storeId})");

        $this->_connection->dropTable(
            $this->_getTemporaryTableName($this->_locationIndexerHelper->getFlatTableName($storeId))
        );
        $this->_connection->createTable($table);
    }

    /**
     * Fill temporary flat table by data from temporary flat table parts
     *
     * @param array $tables
     * @param int|string $storeId
     * @param string $valueFieldSuffix
     * @return void
     */
    protected function _fillTemporaryFlatTable(array $tables, $storeId, $valueFieldSuffix)
    {
        $select = $this->_connection->select();
        $temporaryFlatTableName = $this->_getTemporaryTableName(
            $this->_locationIndexerHelper->getFlatTableName($storeId)
        );
        $flatColumns = $this->_locationIndexerHelper->getFlatColumns();
        $entityTableName = $this->_locationIndexerHelper->getTable('omnyfy_vendor_location_entity');
        $entityTemporaryTableName = $this->_getTemporaryTableName($entityTableName);
        $columnsList = array_keys($tables[$entityTableName]);
        $websiteId = (int)$this->_storeManager->getStore($storeId)->getWebsiteId();

        unset($tables[$entityTableName]);

        $allColumns = array_merge(['entity_id', 'vendor_type_id', 'attribute_set_id'], $columnsList);

        $select->from(
            ['e' => $entityTemporaryTableName],
            $allColumns
        )->joinInner(
            ['ve' => $this->_locationIndexerHelper->getTable('omnyfy_vendor_vendor_entity')],
            'e.vendor_id=ve.entity_id',
            []
        )->joinInner(
            ['p' => $this->_locationIndexerHelper->getTable('omnyfy_vendor_profile')],
            'e.vendor_id=p.vendor_id AND p.website_id='. $websiteId,
            []
        )->joinInner(
            ['pl' => $this->_locationIndexerHelper->getTable('omnyfy_vendor_profile_location')],
            'e.entity_id=pl.location_id AND pl.profile_id=p.profile_id',
            []
        )
        ;

        foreach ($tables as $tableName => $columns) {
            $columnValueNames = [];
            $temporaryTableName = $this->_getTemporaryTableName($tableName);
            $temporaryValueTableName = $temporaryTableName . $valueFieldSuffix;
            $columnsNames = array_keys($columns);

            $select->joinLeft(
                $temporaryTableName,
                'e.entity_id = ' . $temporaryTableName . '.entity_id',
                $columnsNames
            );
            $allColumns = array_merge($allColumns, $columnsNames);

            foreach ($columnsNames as $name) {
                $columnValueName = $name . $valueFieldSuffix;
                if (isset($flatColumns[$columnValueName])) {
                    $columnValueNames[] = $columnValueName;
                }
            }
            if (!empty($columnValueNames)) {
                $select->joinLeft(
                    $temporaryValueTableName,
                    'e.entity_id = ' . $temporaryValueTableName . '.entity_id',
                    $columnValueNames
                );
                $allColumns = array_merge($allColumns, $columnValueNames);
            }
        }
        $sql = $select->insertFromSelect($temporaryFlatTableName, $allColumns, false);
        $this->_connection->query($sql);
    }

    /**
     * Apply diff. between 0 store and current store to temporary flat table
     *
     * @param array $tables
     * @param array $changedIds
     * @param int|string $storeId
     * @param string $valueFieldSuffix
     * @return void
     */
    protected function _updateTemporaryTableByStoreValues(
        array $tables,
        array $changedIds,
        $storeId,
        $valueFieldSuffix
    ) {
        $flatColumns = $this->_locationIndexerHelper->getFlatColumns();
        $temporaryFlatTableName = $this->_getTemporaryTableName(
            $this->_locationIndexerHelper->getFlatTableName($storeId)
        );

        foreach ($tables as $tableName => $columns) {
            foreach ($columns as $attribute) {
                /* @var $attribute \Magento\Eav\Model\Entity\Attribute */
                $attributeCode = $attribute->getAttributeCode();
                if ($attribute->getBackend()->getType() != 'static') {
                    $joinCondition = 't.entity_id = e.entity_id' .
                        ' AND t.attribute_id=' .
                        $attribute->getId() .
                        ' AND t.store_id = ' .
                        $storeId .
                        ' AND t.value IS NOT NULL';
                    /** @var $select \Magento\Framework\DB\Select */
                    $select = $this->_connection->select()->joinInner(
                        ['t' => $tableName],
                        $joinCondition,
                        [$attributeCode => 't.value']
                    );
                    if (!empty($changedIds)) {
                        $select->where($this->_connection->quoteInto('e.entity_id IN (?)', $changedIds));
                    }
                    $sql = $select->crossUpdateFromSelect(['e' => $temporaryFlatTableName]);
                    $this->_connection->query($sql);
                }

                //Update not simple attributes (eg. dropdown)
                if (isset($flatColumns[$attributeCode . $valueFieldSuffix])) {
                    $select = $this->_connection->select()->joinInner(
                        ['t' => $this->_locationIndexerHelper->getTable('eav_attribute_option_value')],
                        't.option_id = e.' . $attributeCode . ' AND t.store_id=' . $storeId,
                        [$attributeCode . $valueFieldSuffix => 't.value']
                    );
                    if (!empty($changedIds)) {
                        $select->where($this->_connection->quoteInto('e.entity_id IN (?)', $changedIds));
                    }
                    $sql = $select->crossUpdateFromSelect(['e' => $temporaryFlatTableName]);
                    $this->_connection->query($sql);
                }
            }
        }
    }

    /**
     * Retrieve temporary table name by regular table name
     *
     * @param string $tableName
     * @return string
     */
    protected function _getTemporaryTableName($tableName)
    {
        return sprintf('%s_tmp_indexer', $tableName);
    }

}
