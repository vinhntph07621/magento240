<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-01
 * Time: 17:41
 */
namespace Omnyfy\VendorSubscription\Model\Resource;

class Plan extends \Omnyfy\Core\Model\ResourceModel\AbstractDbModel
{
    protected function _construct()
    {
        $this->_init('omnyfy_vendorsubscription_plan', 'plan_id');
    }

    protected function getUpdateFields()
    {
        return [
            'plan_name',
            'price',
            'interval',
            'status',
            'gateway_id',
            'description',
            'benefits',
            'button_label',
            'promo_text'
        ];
    }

    public function getRolePlanByVendorTypeId($vendorTypeId)
    {
        if (empty($vendorTypeId)) {
            return false;
        }

        $conn = $this->getConnection();
        $table = $conn->getTableName('omnyfy_vendorsubscription_vendor_type_plan');
        $select = $conn->select()
            ->from($table)
            ->where('type_id=?', $vendorTypeId)
        ;

        $rows = $conn->fetchAll($select);
        $result = [];
        foreach($rows as $row) {
            $key = $row['plan_id'];
            $config = [];
            if (!empty($row['config'])) {
                try {
                    $config = json_decode($row['config'], true);
                }
                catch (\Exception $e) {
                }
            }
            $result[$key] = [
                'role_id' => $row['role_id'],
                'plan_id' => $row['plan_id'],
                'config' => $config
            ];
        }
        return empty($result) ? false : array_values($result);
    }

    public function saveRolePlans($data)
    {
        if (!empty($data)) {
            foreach($data as $i => $row)
            if (isset($row['config'])) {
                if (empty($row['config'])) {
                    $data[$i]['config'] = null;
                }
                else{
                    $data[$i]['config'] = json_encode($row['config']);
                }
            }
        }

        $this->saveToTable('omnyfy_vendorsubscription_vendor_type_plan', $data);
    }

    public function getPlanIdsByVendorTypeId($vendorTypeId)
    {
        if (empty($vendorTypeId)) {
            return false;
        }

        $conn = $this->getConnection();
        $table = $conn->getTableName('omnyfy_vendorsubscription_vendor_type_plan');
        $select = $conn->select()
            ->from($table)
            ->where('type_id=?', $vendorTypeId)
        ;

        $rows = $conn->fetchAll($select);
        $result = [];
        foreach($rows as $row) {
            $key = $row['plan_id'];
            $result[$key] = 1;
        }
        return empty($result) ? false : array_keys($result);
    }

    public function getRoleIdMapByVendorTypeId($vendorTypeId)
    {
        if (empty($vendorTypeId)) {
            return false;
        }

        $conn = $this->getConnection();
        $table = $conn->getTableName('omnyfy_vendorsubscription_vendor_type_plan');
        $select = $conn->select()
            ->from($table)
            ->where('type_id=?', $vendorTypeId)
        ;

        $rows = $conn->fetchAll($select);
        $result = [];
        foreach($rows as $row) {
            $key = $row['plan_id'];
            $result[$key] = $row['role_id'];
        }
        return empty($result) ? false : $result;
    }

    public function loadAllTypePlanRelation()
    {
        $conn = $this->getConnection();
        $typeTable = $conn->getTableName('omnyfy_vendor_vendor_type');
        $sub = $conn->select()
            ->from($typeTable, ['type_id'])
            ->where('status=?', 1)
        ;
        $table = $conn->getTableName('omnyfy_vendorsubscription_vendor_type_plan');
        $select = $conn->select()
            ->from($table)
            ->where('type_id in (?)', $sub)
        ;

        return $conn->fetchAll($select);
    }
}
 