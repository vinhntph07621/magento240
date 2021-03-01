<?php
/**
 * Project: apcd.
 * User: jing
 * Date: 10/9/18
 * Time: 4:25 PM
 */
namespace Omnyfy\Vendor\Model\Resource\Indexer;

class Stock extends \Magento\Indexer\Model\ResourceModel\AbstractResource
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Indexer\Table\StrategyInterface $tableStrategy,
        $connectionName = null
    )
    {
        parent::__construct($context, $tableStrategy, $connectionName);
    }

    public function _construct()
    {
        $this->_init('omnyfy_vendor_inventory_index', 'id');
    }

    public function reindexAll()
    {
        $this->tableStrategy->setUseIdxTable(true);
        $this->beginTransaction();
        try{
            $this->_prepareIndexTable();
            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }

    protected function _getStockSelect($entityIds)
    {
        $connection = $this->getConnection();
        $inventoryTable = $this->getTable('omnyfy_vendor_inventory');

        $locationTable = $this->getTable('omnyfy_vendor_location_entity');

        $vendorTable = $this->getTable('omnyfy_vendor_vendor_entity');

        $profileTable = $this->getTable('omnyfy_vendor_profile');

        //profile location table
        $profileLocationTable = $this->getTable('omnyfy_vendor_profile_location');

        $select = $connection->select()
            ->from(
                ['i' => $inventoryTable],
                ['product_id' => 'i.product_id', 'location_id' => 'i.location_id']
            )
            ->join(
                ['l' => $locationTable],
                'l.entity_id=i.location_id',
                ['priority' => 'l.priority']
            )
            ->join(
                ['v' => $vendorTable],
                'l.vendor_id=v.entity_id',
                ['vendor_id' => 'l.vendor_id']
            )
            ->join(
                ['p' => $profileTable],
                'p.vendor_id=v.entity_id',
                ['website_id' => 'p.website_id']
            )
            ->join(
                ['pl' => $profileLocationTable],
                'pl.location_id=l.entity_id AND pl.profile_id=p.profile_id',
                []
            )
            ->where('i.product_id IN (?)', $entityIds)
        ;

        return $select;
    }

    protected function _prepareIndexTable($entityIds = null)
    {
        $connection = $this->getConnection();
        $select = $this->_getStockSelect($entityIds);
        $query = $select->insertFromSelect($this->getIdxTable());
        $connection->query($query);

        return $this;
    }
}