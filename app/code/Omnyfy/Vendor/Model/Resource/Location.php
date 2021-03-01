<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 6/6/17
 * Time: 11:08 AM
 */

namespace Omnyfy\Vendor\Model\Resource;

use Magento\Eav\Model\Entity\AbstractEntity;
use Magento\Eav\Model\Entity\Context;

class Location extends AbstractEntity
{
    protected $defaultAttributes;

    public function __construct(
        Context $context,
        \Omnyfy\Vendor\Model\Location\Attribute\DefaultAttributes $defaultAttributes,
        $data = []
    ) {
        $this->defaultAttributes = $defaultAttributes;
        parent::__construct($context, $data);
    }

    public function getEntityType()
    {
        if (empty($this->_type)) {
            $this->setType(\Omnyfy\Vendor\Model\Location::ENTITY);
        }
        return parent::getEntityType();
    }

    protected function getUpdateFields()
    {
        return [
            'priority',
            'name',
            'description',
            'address',
            'suburb',
            'region',
            'country',
            'postcode',
            'latitude',
            'longitude',
        ];
    }

    protected function _getDefaultAttributes()
    {
        return $this->defaultAttributes->getDefaultAttributes();
    }

    public function getLocationIdsByProfileIds($profileIds, $checkStatus=false)
    {
        if (empty($profileIds)) {
            return [];
        }

        $conn = $this->getConnection();

        $mainTable = $this->getEntityTable();
        $profileTable = $conn->getTableName('omnyfy_vendor_profile');
        $profileLocationTable = $conn->getTableName('omnyfy_vendor_profile_location');

        $select = $conn->select()->from(
                ['main_table' => $mainTable],
                ['entity_id']
            )->join(
                ['pl' => $profileLocationTable],
                "pl.location_id=main_table.entity_id",
                []
            )->join(
                ['p' => $profileTable],
                "p.profile_id=pl.profile_id",
                []
            )->where(
                "pl.profile_id IN (?)",
                $profileIds
            )
        ;

        if ($checkStatus) {
            $select->where('main_table.status=?', \Omnyfy\Vendor\Api\Data\LocationInterface::STATUS_ENABLED);
        }

        return $conn->fetchCol($select);
    }

    public function saveInventory($data) {
        $this->saveToTable('omnyfy_vendor_inventory', $data, ['qty']);
    }

    public function bulkSave($data) {
        if (empty($data)) {
            return;
        }

        $conn = $this->getConnection();

        $conn->insertOnDuplicate(
            $this->getEntityTable(),
            $data,
            $this->getUpdateFields()
        );
    }

    public function remove($data, $table=null) {
        if (empty($data)) {
            return;
        }

        $conn = $this->getConnection();

        $condition = [];
        foreach($data as $key => $values) {
            if (is_string($key) && !is_numeric($key)) {
                if (is_array($values)) {
                    $condition[] = $conn->quoteInto($key. ' IN (?}', $values);
                }
                else{
                    $condition[] = $conn->quoteInto($key. '=?', $values);
                }
            }
        }

        if (empty($condition)) {
            return;
        }

        $table = empty($table) ? $this->getEntityTable() : $table;
        $conn->delete($table, $condition);
    }

    protected function saveToTable($table, $data, $updateColumns=[]) {
        if (empty($table) || empty($data)) {
            return;
        }

        $conn = $this->getConnection();

        $tableName = $this->getTable($table);

        if (empty($conn) || empty($tableName)) {
            return;
        }

        $conn->insertOnDuplicate($tableName, $data, $updateColumns);
    }

    public function getWarehouseIds($checkStatus=false)
    {
        $conn = $this->getConnection();

        $tableName = $this->getEntityTable();

        $select = $conn->select()->from(
            ['main_table' => $tableName],
            ['entity_id']
        )->where(
            "main_table.is_warehouse=1"
        )
        ;

        if ($checkStatus) {
            $select->where('main_table.status=?', \Omnyfy\Vendor\Api\Data\LocationInterface::STATUS_ENABLED);
        }

        return $conn->fetchCol($select);
    }

    public function getVendorIdsByLocationIds($locationIds, $checkStatus=false)
    {
        if (empty($locationIds)) {
            return [];
        }

        $conn = $this->getConnection();

        $table = $this->getEntityTable();

        $select = $conn->select()->from(
            $table,
            ['entity_id', 'vendor_id']
        )
            ->where(
                "entity_id IN (?)",
                $locationIds
            )
        ;

        if ($checkStatus) {
            $select->where('status = ?', \Omnyfy\Vendor\Api\Data\LocationInterface::STATUS_ENABLED);
        }

        $rows = $conn->fetchAll($select);
        $result = [];
        foreach($rows as $row) {
            $locationId = $row['entity_id'];
            $result[$locationId] = $row['vendor_id'];
        }
        return $result;
    }

    public function getAllLocationIdsToVendorIds($checkStatus=false)
    {
        $conn = $this->getConnection();

        $table = $this->getEntityTable();

        $select = $conn->select()->from(
            $table,
            ['entity_id', 'vendor_id']
        );

        if ($checkStatus) {
            $select->where("status = ?", \Omnyfy\Vendor\Api\Data\LocationInterface::STATUS_ENABLED);
        }

        $rows = $conn->fetchAll($select);
        $result = [];
        foreach($rows as $row) {
            $locationId = $row['entity_id'];
            $result[$locationId] = $row['vendor_id'];
        }
        return $result;
    }
}