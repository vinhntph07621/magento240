<?php
/**
 * Project: Multi Vendors.
 * User: jing
 * Date: 29/1/18
 * Time: 5:32 PM
 */
namespace Omnyfy\Vendor\Model\Resource;

class Inventory extends \Omnyfy\Core\Model\ResourceModel\AbstractDbModel
{
    protected function _construct()
    {
        $this->_init('omnyfy_vendor_inventory', 'inventory_id');
    }

    protected function getUpdateFields()
    {
        return [
            'qty',
        ];
    }

    public function addProductIdsToLocation($productIds, $locationId, $defaultQty=0)
    {
        if (empty($productIds) || empty($locationId)) {
            return;
        }

        $conn = $this->getConnection();

        $productTable = $conn->getTableName('catalog_product_entity');
        $zendDbExprNull = new \Zend_Db_Expr('NULL');
        $select = $conn->select()->from($productTable,
            [
                'inventory_id' => $zendDbExprNull,
                'product_id' => 'entity_id',
                'location_id' => new \Zend_Db_Expr( $locationId ),
                'qty' => new \Zend_Db_Expr(intval($defaultQty))
            ]
        )
        ->where('entity_id IN (?)', $productIds)
        ;
        $insertQuery = $conn->insertFromSelect($select, $this->getMainTable(),
            [
                'inventory_id', 'product_id', 'location_id', 'qty'
            ],
            \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_IGNORE
        );

        $conn->query($insertQuery);
    }

    public function removeByVendorIds($productIds, $vendorIds)
    {
        if (empty($productIds) || empty($vendorIds)) {
            return;
        }

        $conn = $this->getConnection();

        $locationTable = $conn->getTableName('omnyfy_vendor_location_entity');

        $conn->delete(
            $this->getMainTable(),
            [
                'product_id IN (?)' => $productIds,
                'location_id IN (SELECT entity_id FROM '. $locationTable.' WHERE vendor_id IN (?))' => $vendorIds,
            ]
        );
    }

    public function removeByNotInVendorIds($productIds, $vendorIds)
    {
        if (empty($productIds) || empty($vendorIds)) {
            return;
        }

        $conn = $this->getConnection();

        $locationTable = $conn->getTableName('omnyfy_vendor_location_entity');

        $conn->delete(
            $this->getMainTable(),
            [
                'product_id IN (?)' => $productIds,
                'location_id IN (SELECT entity_id FROM '. $locationTable.' WHERE vendor_id NOT IN (?))' => $vendorIds,
            ]
        );
    }

    public function updateQty($inventoryId, $qty)
    {
        if (empty($inventoryId) || empty($qty)) {
            return;
        }

        $conn = $this->getConnection();

        $conn->update(
            $this->getMainTable(),
            ['qty' => $qty],
            ['inventory_id=?' => $inventoryId]
        );
    }

    public function loadInventoryGroupedByLocation($productId, $websiteId, &$vendorId, $activeVendorOnly=false, $activeLocationOnly=false)
    {
        if (!is_array($productId)) {
            $productId = [$productId];
        }

        $websiteIds = [];
        if (!empty($websiteId)) {
            $websiteIds[] = $websiteId;
        }

        $conn = $this->getConnection();

        $inventoryTable = $this->getMainTable();

        $locationTable = $this->getTable('omnyfy_vendor_location_entity');

        $vendorTable = $this->getTable('omnyfy_vendor_vendor_entity');

        $profileTable = $this->getTable('omnyfy_vendor_profile');

        //profile location table
        $profileLocationTable = $this->getTable('omnyfy_vendor_profile_location');

        $vendorStatusCondition = $activeVendorOnly ? ' AND v.status=' . \Omnyfy\Vendor\Api\Data\VendorInterface::STATUS_ENABLED : '';

        $locationStatusCondition = $activeLocationOnly ? ' AND l.status=' . \Omnyfy\Vendor\Api\Data\LocationInterface::STATUS_ENABLED : '';

        $select = $conn->select()
            ->from(['i' => $inventoryTable])
            ->join(
                ['l' => $locationTable],
                'l.entity_id=i.location_id' . $locationStatusCondition,
                ['priority' => 'l.priority']
            )
            ->join(
                ['v' => $vendorTable],
                'l.vendor_id=v.entity_id' . $vendorStatusCondition,
                ['vendor_id' => 'l.vendor_id']
            )
            ->join(
                ['p' => $profileTable],
                'p.vendor_id=v.entity_id',
                []
            )
            ->join(
                ['pl' => $profileLocationTable],
                'pl.location_id=l.entity_id AND pl.profile_id=p.profile_id',
                []
            )
            ->where('i.product_id IN (?)', $productId)
        ;

        if (!empty($websiteIds)) {
            $select->where('p.website_id IN (?)', $websiteIds);
        }

        $select->order('priority');

        $result = [];
        $dataSet = $conn->fetchAll($select);
        foreach ($dataSet as $raw) {
            $result[$raw['location_id']] = $raw['qty'];
            $vendorId = $raw['vendor_id'];
        }

        return $result;
    }

    public function loadQtysByProductIds($productIds, $websiteId, $activeVendorOnly=false, $activeLocationOnly=false)
    {
        if (!is_array($productIds)) {
            $productIds = [$productIds];
        }

        $websiteIds = [];
        if (!empty($websiteId)) {
            $websiteIds[] = $websiteId;
        }

        $conn = $this->getConnection();

        $inventoryTable = $this->getMainTable();

        $locationTable = $this->getTable('omnyfy_vendor_location_entity');

        $vendorTable = $this->getTable('omnyfy_vendor_vendor_entity');

        $profileTable = $this->getTable('omnyfy_vendor_profile');

        //profile location table
        $profileLocationTable = $this->getTable('omnyfy_vendor_profile_location');

        $vendorStatusCondition = $activeVendorOnly ? ' AND v.status=' . \Omnyfy\Vendor\Api\Data\VendorInterface::STATUS_ENABLED : '';

        $locationStatusCondition = $activeLocationOnly ? ' AND l.status=' . \Omnyfy\Vendor\Api\Data\LocationInterface::STATUS_ENABLED : '';

        $select = $conn->select()
            ->from(['i' => $inventoryTable])
            ->join(
                ['l' => $locationTable],
                'l.entity_id=i.location_id' . $locationStatusCondition,
                ['priority' => 'l.priority']
            )
            ->join(
                ['v' => $vendorTable],
                'l.vendor_id=v.entity_id' . $vendorStatusCondition,
                ['vendor_id' => 'l.vendor_id']
            )
            ->join(
                ['p' => $profileTable],
                'p.vendor_id=v.entity_id',
                []
            )
            ->join(
                ['pl' => $profileLocationTable],
                'pl.location_id=l.entity_id AND pl.profile_id=p.profile_id',
                []
            )
            ->where('i.product_id IN (?)', $productIds)
        ;

        if (!empty($websiteIds)) {
            $select->where('p.website_id IN (?)', $websiteIds);
        }

        $select->order(['product_id', 'priority']);

        $result = [];
        $dataSet = $conn->fetchAll($select);
        foreach ($dataSet as $raw) {
            $productId = $raw['product_id'];
            if (!array_key_exists($productId, $result)) {
                $result[$productId] = [];
            }
            $result[$productId][$raw['location_id']] = $raw['qty'];
        }

        return $result;
    }
}