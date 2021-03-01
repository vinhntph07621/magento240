<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-06-04
 * Time: 16:35
 */
namespace Omnyfy\Vendor\Model\Indexer\Vendor\Flat\Action\Rows;

class TableData implements \Omnyfy\Vendor\Model\Indexer\Vendor\Flat\TableDataInterface
{
    protected $_connection;

    protected $_vendorIndexerHelper;

    protected $_resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Omnyfy\Vendor\Helper\Vendor\Flat\Indexer $vendorIndexerHelper
    ) {
        $this->_resource = $resource;
        $this->_vendorIndexerHelper = $vendorIndexerHelper;
    }

    public function move($flatTable, $flatDropName, $temporaryFlatTableName)
    {
        $connection = $this->_resource->getConnection();
        if (!$connection->isTableExists($flatTable)) {
            $connection->dropTable($flatDropName);
            $connection->renameTablesBatch([['oldName' => $temporaryFlatTableName, 'newName' => $flatTable]]);
            $connection->dropTable($flatDropName);
        } else {
            $describe = $connection->describeTable($flatTable);
            $columns = $this->_vendorIndexerHelper->getFlatColumns();
            $columns = array_keys(array_intersect_key($describe, $columns));
            $select = $connection->select();

            $select->from(['tf' => sprintf('%s_tmp_indexer', $flatTable)], $columns);
            $sql = $select->insertFromSelect($flatTable, $columns);
            $connection->query($sql);

            $connection->dropTable(sprintf('%s_tmp_indexer', $flatTable));
        }
    }
}