<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-08-15
 * Time: 15:36
 */
namespace Omnyfy\VendorSubscription\Model\Resource;

class Usage extends \Omnyfy\Core\Model\ResourceModel\AbstractDbModel
{
    protected function _construct()
    {
        $this->_init('omnyfy_vendorsubscription_usage', 'id');
    }

    protected function getUpdateFields()
    {
        return [
            'vendor_id',
            'usage_type_id',
            'usage_limit',
            'usage_count',
            'start_date',
            'end_date'
        ];
    }

    public function logUsage($vendorId, $usageTypeId, $id)
    {
        $conn = $this->getConnection();
        $table = $conn->getTableName('omnyfy_vendorsubscription_usage_log');
        $this->saveToTable($table,
            [
                'vendor_id' => $vendorId,
                'usage_type_id' => $usageTypeId,
                'object_id' => $id,
                'is_deleted' => 0
            ]
        );
    }

    public function getLogTotal($vendorId, $usageTypeId)
    {
        $conn = $this->getConnection();
        $table = $conn->getTableName('omnyfy_vendorsubscription_usage_log');
        $select = $conn->select()
            ->from($table, ['total' => 'COUNT(*)'])
            ->where('vendor_id=?', $vendorId)
            ->where('usage_type_id=?', $usageTypeId)
            ->where('is_deleted=?', 0)
        ;
        return $conn->fetchOne($select);
    }

    public function removeUsageLog($vendorId, $usageTypeId, $id)
    {
        $conn = $this->getConnection();
        $table = $conn->getTableName('omnyfy_vendorsubscription_usage_log');

        $conn->update($table,
            [
                'is_deleted' => 1
            ],
            [
                'vendor_id=?' => $vendorId,
                'usage_type_id=?' => $usageTypeId,
                'object_id=?' => $id
            ]
        );
    }

    public function loadPlanUsageRelation($planId)
    {
        $conn = $this->getConnection();
        $planUsageTable = $conn->getTableName('omnyfy_vendorsubscription_plan_usage');
        $select = $conn->select()
            ->from($planUsageTable)
            ->where('plan_id=?', $planId)
        ;
        $rows = $conn->fetchAll($select);
        $result = [];
        foreach($rows as $row) {
            $usageTypeId = $row['usage_type_id'];
            $result[$usageTypeId] = $row['usage_limit'];
        }

        return empty($result) ? false : $result;
    }

    public function loadPackageUsageRelation($packageId)
    {
        $conn = $this->getConnection();
        $packageUsageTable = $conn->getTableName('omnyfy_vendorsubscription_package_usage');
        $select = $conn->select()
            ->from($packageUsageTable)
            ->where('package_id=?', $packageId)
        ;
        $rows = $conn->fetchAll($select);
        $result = [];
        foreach($rows as $row) {
            $usageTypeId = $row['usage_type_id'];
            $result[$usageTypeId] = [
                'usage_limit' => $row['usage_limit'],
                'interval' => $row['interval']
            ];
        }

        return empty($result) ? false : $result;
    }

    public function savePlanUsage($planId, $data)
    {
        $current = [];
        $data = empty($data) ? [] : $data;
        foreach($data as $row) {
            $current[$row['usage_type_id']] = $row['usage_limit'];
        }
        $planUsage = $this->loadPlanUsageRelation($planId);

        $toRemove = [];
        if (!empty($planUsage)) {
            foreach($planUsage as $typeId => $limit) {
                if (!array_key_exists($typeId, $current)) {
                    $toRemove[] = $typeId;
                }
            }
        }

        $conn = $this->getConnection();
        $table = $conn->getTableName('omnyfy_vendorsubscription_plan_usage');

        if (!empty($toRemove)) {
            $conn->delete($table, [
                'plan_id=?' => $planId,
                'usage_type_id IN (?)' => $toRemove
            ]);
        }

        $toSave = [];
        $zendDbExprNull = new \Zend_Db_Expr('NULL');
        foreach($data as $row) {
            $toSave[] = [
                'id' => $zendDbExprNull,
                'plan_id' => $planId,
                'usage_type_id' => $row['usage_type_id'],
                'usage_limit' => $row['usage_limit']
            ];
        }

        $this->saveToTable($table, $toSave, ['usage_limit']);
    }
}
 