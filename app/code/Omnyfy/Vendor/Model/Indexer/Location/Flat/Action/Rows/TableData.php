<?php

namespace Omnyfy\Vendor\Model\Indexer\Location\Flat\Action\Rows;

use Magento\Framework\App\ResourceConnection;

/**
 * Class TableData
 */
class TableData implements \Omnyfy\Vendor\Model\Indexer\Location\Flat\TableDataInterface
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * @var \Omnyfy\Vendor\Helper\Location\Flat\Indexer
     */
    protected $_locationIndexerHelper;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Omnyfy\Vendor\Helper\Location\Flat\Indexer $locationIndexerHelper
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Omnyfy\Vendor\Helper\Location\Flat\Indexer $locationIndexerHelper
    ) {
        $this->_resource = $resource;
        $this->_locationIndexerHelper = $locationIndexerHelper;
    }

    /**
     * Move data from temporary tables to flat
     *
     * @param string $flatTable
     * @param string $flatDropName
     * @param string $temporaryFlatTableName
     * @return void
     */
    public function move($flatTable, $flatDropName, $temporaryFlatTableName)
    {
        $connection = $this->_resource->getConnection();
        if (!$connection->isTableExists($flatTable)) {
            $connection->dropTable($flatDropName);
            $connection->renameTablesBatch([['oldName' => $temporaryFlatTableName, 'newName' => $flatTable]]);
            $connection->dropTable($flatDropName);
        } else {
            $describe = $connection->describeTable($flatTable);
            $columns = $this->_locationIndexerHelper->getFlatColumns();
            $columns = array_keys(array_intersect_key($describe, $columns));
            $select = $connection->select();

            $select->from(['tf' => sprintf('%s_tmp_indexer', $flatTable)], $columns);
            $sql = $select->insertFromSelect($flatTable, $columns);
            $connection->query($sql);

            $connection->dropTable(sprintf('%s_tmp_indexer', $flatTable));
        }
    }
}
