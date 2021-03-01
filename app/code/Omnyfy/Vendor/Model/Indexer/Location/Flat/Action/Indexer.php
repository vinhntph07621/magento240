<?php

namespace Omnyfy\Vendor\Model\Indexer\Location\Flat\Action;

use Magento\Framework\App\ResourceConnection;

class Indexer
{
    /**
     * Maximum size of attributes chunk
     */
    const ATTRIBUTES_CHUNK_SIZE = 59;

    /**
     * @var \Omnyfy\Vendor\Helper\Location\Flat\Indexer
     */
    protected $_locationIndexerHelper;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;
    /**
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Omnyfy\Vendor\Helper\Location\Flat\Indexer $locationHelper
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Omnyfy\Vendor\Helper\Location\Flat\Indexer $locationHelper
    ) {
        $this->_locationIndexerHelper = $locationHelper;
        $this->_connection = $resource->getConnection();
    }

    /**
     * Write single location into flat location table
     *
     * @param int $storeId
     * @param int $locationId
     * @param string $valueFieldSuffix
     * @return \Omnyfy\Vendor\Model\Indexer\Location\Flat
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function write($storeId, $locationId, $valueFieldSuffix = '')
    {
        $flatTable = $this->_locationIndexerHelper->getFlatTableName($storeId);

        $attributes = $this->_locationIndexerHelper->getAttributes();
        $eavAttributes = $this->_locationIndexerHelper->getTablesStructure($attributes);
        $updateData = [];
        $describe = $this->_connection->describeTable($flatTable);

        foreach ($eavAttributes as $tableName => $tableColumns) {
            $columnsChunks = array_chunk($tableColumns, self::ATTRIBUTES_CHUNK_SIZE, true);

            foreach ($columnsChunks as $columns) {
                $select = $this->_connection->select();
                $selectValue = $this->_connection->select();
                $keyColumns = [
                    'entity_id' => 'e.entity_id',
                    'attribute_id' => 't.attribute_id',
                    'value' => $this->_connection->getIfNullSql('`t2`.`value`', '`t`.`value`'),
                ];

                if ($tableName != $this->_locationIndexerHelper->getTable('omnyfy_vendor_location_entity')) {
                    $valueColumns = [];
                    $ids = [];
                    $select->from(
                        ['e' => $this->_locationIndexerHelper->getTable('omnyfy_vendor_location_entity')],
                        $keyColumns
                    );

                    $selectValue->from(
                        ['e' => $this->_locationIndexerHelper->getTable('omnyfy_vendor_location_entity')],
                        $keyColumns
                    );

                    /** @var $attribute \Omnyfy\Vendor\Model\Resource\Eav\Attribute */
                    foreach ($columns as $columnName => $attribute) {
                        if (isset($describe[$columnName])) {
                            $ids[$attribute->getId()] = $columnName;
                        }
                    }

                    $select->joinLeft(
                        ['t' => $tableName],
                        'e.entity_id = t.entity_id ' . $this->_connection->quoteInto(
                            ' AND t.attribute_id IN (?)',
                            array_keys($ids)
                        ) . ' AND t.store_id = 0',
                        []
                    )->joinLeft(
                        ['t2' => $tableName],
                        't.entity_id = t2.entity_id ' .
                        ' AND t.attribute_id = t2.attribute_id  ' .
                        $this->_connection->quoteInto(
                            ' AND t2.store_id = ?',
                            $storeId
                        ),
                        []
                    )->where(
                        'e.entity_id = ' . $locationId
                    )->where(
                        't.attribute_id IS NOT NULL'
                    );
                    $cursor = $this->_connection->query($select);
                    while ($row = $cursor->fetch(\Zend_Db::FETCH_ASSOC)) {
                        $updateData[$ids[$row['attribute_id']]] = $row['value'];
                        $valueColumnName = $ids[$row['attribute_id']] . $valueFieldSuffix;
                        if (isset($describe[$valueColumnName])) {
                            $valueColumns[$row['value']] = $valueColumnName;
                        }
                    }

                    //Update not simple attributes (eg. dropdown)
                    if (!empty($valueColumns)) {
                        $valueIds = array_keys($valueColumns);

                        $select = $this->_connection->select()->from(
                            ['t' => $this->_locationIndexerHelper->getTable('eav_attribute_option_value')],
                            ['t.option_id', 't.value']
                        )->where(
                            $this->_connection->quoteInto('t.option_id IN (?)', $valueIds)
                        );
                        $cursor = $this->_connection->query($select);
                        while ($row = $cursor->fetch(\Zend_Db::FETCH_ASSOC)) {
                            $valueColumnName = $valueColumns[$row['option_id']];
                            if (isset($describe[$valueColumnName])) {
                                $updateData[$valueColumnName] = $row['value'];
                            }
                        }
                    }
                } else {
                    $columnNames = array_keys($columns);
                    $select->from(
                        ['e' => $this->_locationIndexerHelper->getTable('omnyfy_vendor_location_entity')],
                        $columnNames
                    )->where(
                        'e.entity_id = ' . $locationId
                    );
                    $cursor = $this->_connection->query($select);
                    $row = $cursor->fetch(\Zend_Db::FETCH_ASSOC);
                    if (!empty($row)) {
                        foreach ($row as $columnName => $value) {
                            $updateData[$columnName] = $value;
                        }
                    }
                }
            }
        }

        if (!empty($updateData)) {
            $updateData += ['entity_id' => $locationId];
            $updateFields = [];
            foreach ($updateData as $key => $value) {
                $updateFields[$key] = $key;
            }
            $this->_connection->insertOnDuplicate($flatTable, $updateData, $updateFields);
        }

        return $this;
    }
}
