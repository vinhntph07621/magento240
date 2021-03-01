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

class Vendor extends AbstractEntity
{
    protected $defaultAttributes;

    public function __construct(
        Context $context,
        \Omnyfy\Vendor\Model\Vendor\Attribute\DefaultAttributes $defaultAttributes,
        $data = []
    ) {
        $this->defaultAttributes = $defaultAttributes;
        parent::__construct($context, $data);
    }

    public function getEntityType()
    {
        if (empty($this->_type)) {
            $this->setType(\Omnyfy\Vendor\Model\Vendor::ENTITY);
        }
        return parent::getEntityType();
    }

    protected function getUpdateFields()
    {
        return [
            'name',
            'status',
            'address',
            'phone',
            'email',
            'fax',
            'social_media',
            'description',
            'abn',
            'logo',
            'banner',
            'type_id',
            'attribute_set_id'
        ];
    }

    /**
     * Default vendor attributes
     *
     * @return string[]
     */
    protected function _getDefaultAttributes()
    {
        return $this->defaultAttributes->getDefaultAttributes();
    }

    /**
     * @param $orderIdToVendorId
     */
    public function saveOrderRelation($orderIdToVendorId)
    {
        $this->saveToTable('omnyfy_vendor_vendor_order', $orderIdToVendorId);
    }

    /**
     * @param $customerIdToVendorId
     */
    public function saveCustomerRelation($customerIdToVendorId)
    {
        $this->saveToTable('omnyfy_vendor_vendor_customer', $customerIdToVendorId);
    }

    /**
     * @param $productIdToVendorId
     */
    public function saveProductRelation($productIdToVendorId)
    {
        $this->saveToTable('omnyfy_vendor_vendor_product', $productIdToVendorId);
    }

    public function saveInvoiceRelation($invoiceIdToVendorId)
    {
        $this->saveToTable('omnyfy_vendor_vendor_invoice', $invoiceIdToVendorId);
    }

    public function saveUserRelation($userIdToVendorId)
    {
        $this->saveToTable('omnyfy_vendor_vendor_admin_user', $userIdToVendorId);
    }

    public function saveOrderTotal($vendorOrderTotal, array $updateColumns=array())
    {
        $this->saveToTable('omnyfy_vendor_order_total', $vendorOrderTotal, $updateColumns);
    }

    public function saveInvoiceTotal($vendorInvoiceTotal, array $updateColumns=array())
    {
        $this->saveToTable('omnyfy_vendor_invoice_total', $vendorInvoiceTotal, $updateColumns);
    }

    public function updateVendorByUserId($userId, $vendorId)
    {
        $conn = $this->getConnection();
        $table = $conn->getTableName('omnyfy_vendor_vendor_admin_user');

        $doesUserExistInAdmin = $this->doesUserExistInAdmin($userId);

        if ($doesUserExistInAdmin && count($doesUserExistInAdmin) > 0) {
            $sql = "UPDATE ". $table . " SET vendor_id=? WHERE user_id=?";
            $conn->query($sql, [intval($vendorId), intval($userId)]);
        } elseif (!$doesUserExistInAdmin) {
            $sql= "INSERT INTO " . $table . "(user_id, vendor_id) VALUES(".$userId.",".$vendorId .")";
            $conn->query($sql);
        }
    }

    public function doesUserExistInAdmin($userId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_vendor_vendor_admin_user');

        $select = $conn->select()->from(
            $table,
            ['user_id']
        )->where(
            "user_id = ?",
            $userId
        );

        return $conn->fetchOne($select);
    }

    public function shouldUpdateVendor($userId, $vendorId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_vendor_vendor_admin_user');

        $select = $conn->select()->from(
            $table,
            ['vendor_id']
        )->where('user_id = ' . $userId);

        $result = $conn->fetchOne($select);

        // if result is false set the vendor
        if (!$result) {
            return 'should_assign';
        } else if ($result) {
            // if the current set vendor isnt the same as existing return true
            if ($result != $vendorId) {
                // Check if vendor is already assigned to another user
                $isVendorAssigned = $this->isVendorAlreadyAssigned($vendorId);
                if ($isVendorAssigned != false && $isVendorAssigned != $userId) {
                    return 'vendor_assigned';
                }
                return 'should_assign';
            }
        }
    }

    public function isVendorAlreadyAssigned($vendorId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_vendor_vendor_admin_user');

        $select = $conn->select()->from(
            $table,
            ['user_id']
        )->where('vendor_id = ' . $vendorId);

        return $conn->fetchOne($select);
    }

    public function removeUserVendor($userId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_vendor_vendor_admin_user');

        $select = $conn->select()->from(
            $table,
            ['user_id']
        )->where('user_id = ' . $userId);

        $doesUserExist = $conn->fetchOne($select);

        if ($doesUserExist) {
            // remove the user
            $removeQuery = "Delete FROM " . $table . " Where user_id = " . $userId;
            $conn->query($removeQuery);
        }

    }

    public function getVendorStore($userId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_vendor_vendor_user_stores');

        $select = $conn->select()->from(
            $table,
            ['store_id']
        )->where(
            "user_id = ?",
            $userId
        );

        return $conn->fetchOne($select);
    }

    public function updateUserStores($userId, $stores)
    {
        $conn = $this->getConnection();
        $table = $conn->getTableName('omnyfy_vendor_vendor_user_stores');

        if(!empty($stores)) {
            $storesSerialized = serialize($stores);
        } else {
            $storesSerialized = '';
        }

        $doesUserExistInTable = $this->doesUserStoreExist($userId);

        if($doesUserExistInTable) {
            $sql = "UPDATE ". $table . " SET store_id=? WHERE user_id=?";
            $conn->query($sql, [$storesSerialized, intval($userId)]);
        } elseif (!$doesUserExistInTable) {
            $sql= "INSERT INTO " . $table . "(user_id, store_id) VALUES(".$userId.",'".$storesSerialized ."')";
            $conn->query($sql);
        }
    }

    public function doesUserStoreExist($userId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_vendor_vendor_user_stores');

        $select = $conn->select()->from(
            $table,
            ['user_id']
        )->where(
            "user_id = ?",
            $userId
        );

        return $conn->fetchOne($select);
    }

    public function getVendorIdByUserId($userId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_vendor_vendor_admin_user');

        $select = $conn->select()->from(
            $table,
            ['vendor_id']
        )->where(
            "user_id = ?",
            $userId
        );

        return $conn->fetchOne($select);
    }

    public function getUserIdsByVendorId($vendorId)
    {
        $conn = $this->getConnection();
        $table = $conn->getTableName('omnyfy_vendor_vendor_admin_user');

        $select = $conn->select()->from(
            $table,
            ['user_id']
        )->where(
            "vendor_id = ?",
            $vendorId
        )
        ;

        return $conn->fetchCol($select);
    }

    public function getProductIdsByVendorId($vendorId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_vendor_vendor_product');

        $select = $conn->select()->from(
            $table,
            ['product_id']
        )
        ->where(
            "vendor_id = ?",
            $vendorId
        )
        ;

        return $conn->fetchCol($select);
    }

    public function loadVendorWithProfiles()
    {
        $conn = $this->getConnection();

        $mainTable = $this->getEntityTable();

        $profileTable = $conn->getTableName('omnyfy_vendor_profile');

        $select = $conn->select()->from(['main_table' => $mainTable])
            ->join(
                ['p' => $profileTable],
                'main_table.entity_id=p.vendor_id',
                ['p.website_id', 'p.updates']
            )
            ->where('p.website_id > 0')
            ;
        $rows = $conn->fetchAll($select);
        $result = [];
        foreach($rows as $row) {
            $vendorId = $row['entity_id'];
            if (array_key_exists($vendorId, $result)) {
                $result[$vendorId]['website_ids'][] = $row['website_id'];
            }
            else {
                $result[$vendorId] = $row;
                $result[$vendorId]['website_ids'] =[$row['website_id']];
            }
        }
        return $result;
    }

    public function getInventoryByLocationId($locationId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_vendor_inventory');

        $select = $conn->select()->from($table)
            ->where('location_id=?', $locationId);

        $rows = $conn->fetchAll($select);
        $result = [];
        foreach($rows as $row) {
            $productId = $row['product_id'];
            $result[$productId] = $row['qty'];
        }
        return $result;
    }

    public function getWebsiteIdsByLocationId($locationId)
    {
        if (empty($locationId)) {
            return [];
        }
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_vendor_profile');
        $plTable = $conn->getTableName('omnyfy_vendor_profile_location');

        $select = $conn->select()->from(['p' => $table], ['p.website_id'])
            ->join(
                ['pl' => $plTable],
                'p.profile_id=pl.profile_id',
                ['location_id' => 'pl.location_id']
            )
            ->where('pl.location_id=?', $locationId)
            ;

        $result = $conn->fetchCol($select);
        return empty($result) ? [] : $result;
    }

    public function getWebsiteIds($vendor)
    {
        if ($vendor instanceof \Omnyfy\Vendor\Model\Vendor) {
            $vendorId = $vendor->getEntityId();
        }
        else{
            $vendorId = $vendor;
        }

        $conn = $this->getConnection();

        $select = $conn->select()->from(
            $conn->getTableName('omnyfy_vendor_profile'),
            'website_id'
        )->where(
            'vendor_id=?',
            intval($vendorId)
        );

        return $conn->fetchCol($select);
    }

    public function deductQty($qtyData) {
        if (empty($qtyData)) {
            return;
        }
        $conn = $this->getConnection();
        $table = $conn->getTableName('omnyfy_vendor_inventory');

        $sql = "UPDATE ". $table . " SET qty=qty - ? WHERE product_id=? AND location_id=?";
        foreach($qtyData as $data) {
            $conn->query($sql, [floatval($data['qty']), intval($data['product_id']), intval($data['location_id'])]);
        }
    }

    public function returnQty($qtyData) {
        if (empty($qtyData)) {
            return;
        }
        $conn = $this->getConnection();
        $table = $conn->getTableName('omnyfy_vendor_inventory');

        $sql = "UPDATE ". $table . " SET qty=qty + ? WHERE product_id=? AND location_id=?";
        foreach($qtyData as $data) {
            $conn->query($sql, [floatval($data['qty']), intval($data['product_id']), intval($data['location_id'])]);
        }
    }

    public function getVendorIdByProductId($productId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_vendor_vendor_product');

        $select = $conn->select()->from(
            $table,
            ['vendor_id']
        )
            ->where(
                "product_id = ?",
                $productId
            )
            ->limit(1)
        ;

        return $conn->fetchOne($select);
    }

    public function getVendorIdByProducts($productIds)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_vendor_vendor_product');

        $select = $conn->select()->from(
            $table,
            ['vendor_id', 'product_id']
        )
            ->where(
                "product_id IN (?)",
                $productIds
            )
        ;

        $rows = $conn->fetchAll($select);
        $result = [];
        foreach($rows as $row) {
            $productId = $row['product_id'];
            $result[$productId] = $row['vendor_id'];
        }
        return $result;
    }

    public function getVendorIdArrayByProductIds($productIds)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_vendor_vendor_product');

        $select = $conn->select()->from(
            $table,
            ['vendor_id', 'product_id']
        )
            ->where(
                "product_id IN (?)",
                $productIds
            )
        ;

        $rows = $conn->fetchAll($select);
        $result = [];
        foreach($rows as $row) {
            $productId = $row['product_id'];
            if (!array_key_exists($productId, $result)) {
                $result[$productId] = [];
            }
            $result[$productId][] = $row['vendor_id'];
        }
        return $result;
    }

    public function getVendorIdArrayByProductId($productId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_vendor_vendor_product');

        $select = $conn->select()->from(
            $table,
            ['vendor_id']
        )
            ->where(
                "product_id = ?",
                $productId
            )
        ;

        return $conn->fetchCol($select);
    }

    public function removeProductNotInVendorIds($productIds, $vendorIds)
    {
        if (empty($productIds) || empty($vendorIds)) {
            return;
        }

        $conn = $this->getConnection();

        $conn->delete(
            $conn->getTableName('omnyfy_vendor_vendor_product'),
            [
                'product_id IN (?)' => $productIds,
                'vendor_id NOT IN (?)' => $vendorIds,
            ]
        );
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
                    $condition[] = $conn->quoteInto($key. ' IN (?)', $values);
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

    protected function saveToTable($table, array $data, array $updateColumns=array()) {
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

    public function isOrderForVendor($orderId, $vendorId) {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_vendor_vendor_order');

        $select = $conn->select()->from(
            $table,
            ['cnt' => 'COUNT(*)']
        )
            ->where("order_id = ?", $orderId)
            ->where("vendor_id = ?", $vendorId)
        ;

        $cnt = $conn->fetchOne($select);

        return $cnt > 0 ? true: false;
    }

    public function isInvoiceForVendor($invoiceId, $vendorId) {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_vendor_vendor_invoice');

        $select = $conn->select()->from(
            $table,
            ['cnt' => 'COUNT(*)']
        )
            ->where("invoice_id = ?", $invoiceId)
            ->where("vendor_id = ?", $vendorId)
        ;

        $cnt = $conn->fetchOne($select);

        return $cnt > 0 ? true: false;
    }

    public function isRuleForVendor($ruleId, $vendorId) {
        $conn = $this->getConnection();

        $table = $conn->getTableName('salesrule');

        $select = $conn->select()->from(
            $table,
            [ 'cnt' => 'COUNT(*)' ]
        )
            ->where('rule_id = ?', $ruleId)
            ->where('vendor_id=?', $vendorId)
            ;

        $cnt = $conn->fetchOne($select);

        return $cnt > 0 ? true : false;
    }

    /** START Garment Broker Functionality */
    /* We have used the existing favourite vendor for Garment Broker for GE specific,
    however we still need functionality to add multiple favourites */
    public function getFavoriteVendorIdByCustomerId($customerId) {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_vendor_customer_favorite_vendor');
        $select = $conn->select()->from(
            $table,
            ['vendor_id']
        )
            ->where('customer_id=?', $customerId)
        ;

        return $conn->fetchOne($select);
    }

    public function saveFavoriteVendorId($customerId, $vendorId) {
        if (empty($customerId) || empty($vendorId)) {
            return;
        }

        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_vendor_customer_favorite_vendor');
        $conn->insertOnDuplicate($table,
            ['customer_id' => $customerId, 'vendor_id' => $vendorId],
            ['vendor_id']
        );
    }
    /** END Garment Broker Functionality */

    public function getOrderNumberByOrderId($orderId) {
        $conn = $this->getConnection();

        $table = $conn->getTableName('sales_order');
        $select = $conn->select()->from(
            $table,
            ['increment_id']
        )
            ->where('entity_id=?', $orderId)
        ;

        return $conn->fetchOne($select);
    }

    public function updateLocationAttributeSetId($vendorId, $attributeSetId, $vendorTypeId) {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_vendor_location_entity');
        $conn->update($table,
            [
                'attribute_set_id' => $attributeSetId,
                'vendor_type_id' => $vendorTypeId
            ],
            [ 'vendor_id=?' => $vendorId ]
        );
    }

    public function updateLocationVendorTypeIdToDefault($oldVendorTypeId, $updateAttributeSetId = false) {
        $conn = $this->getConnection();

        $defaultTypeId = 1;
        $data = [
            'vendor_type_id' => $defaultTypeId
        ];

        if ($updateAttributeSetId) {
            $t = $conn->getTableName('eav_entity_type');
            $select = $conn->select()
                ->from($t, ['default_attribute_set_id'])
                ->where('entity_type_code=?', \Omnyfy\Vendor\Model\Location::ENTITY);
            $attributeSetId = $conn->fetchOne($select);
            $data['attribute_set_id'] = $attributeSetId;
        }

        $table = $conn->getTableName('omnyfy_vendor_location_entity');
        $conn->update($table, $data,
            [ 'vendor_type_id=?' => $oldVendorTypeId ]
        );
    }

    public function updateVendorTypeIdToDefault($oldVendorTypeId, $updateAttributeSetId = false) {
        $conn = $this->getConnection();

        $defaultTypeId = 1;
        $data = [
            'type_id' => $defaultTypeId
        ];
        if ($updateAttributeSetId) {
            $t = $conn->getTableName('eav_entity_type');
            $select = $conn->select()
                ->from($t, ['default_attribute_set_id'])
                ->where('entity_type_code=?', \Omnyfy\Vendor\Model\Vendor::ENTITY);
            $attributeSetId = $conn->fetchOne($select);
            $data['attribute_set_id'] = $attributeSetId;
        }
        $table = $conn->getTableName('omnyfy_vendor_vendor_entity');
        $conn->update($table, $data,
            [ 'type_id=?' => $oldVendorTypeId ]
        );
    }

    public function saveQuoteShipping($quoteId, $data) {
        if (empty($quoteId) || empty($data)) {
            return;
        }

        $conn = $this->getConnection();
        $table = $conn->getTableName('omnyfy_vendor_quote_shipping');

        $conn->delete($table, ['quote_id = ?' => $quoteId]);

        $conn->insertOnDuplicate($table, $data,
            [
                'address_id',
                'rate_id',
                'method_code',
                'amount',
                'base_amount',
                'carrier',
                'method_title'
            ]
        );
    }

    public function getQuoteShipping($quoteId) {
        if (empty($quoteId)) {
            return false;
        }

        $conn = $this->getConnection();
        $table = $conn->getTableName('omnyfy_vendor_quote_shipping');

        $select = $conn->select()->from($table)
            ->where("quote_id = ?", $quoteId)
        ;

        $rows = $conn->fetchAll($select);
        $result = [];
        foreach($rows as $row) {
            $locationId = $row['location_id'];
            $result[$locationId] = $row;
        }
        return $result;

    }

    public function getVendorTypeIdByVendorId($vendorId) {
        if (empty($vendorId)) {
            return false;
        }

        $conn = $this->getConnection();
        $table = $this->getEntityTable();

        $select = $conn->select()->from($table, ['type_id'])
            ->where('entity_id = ?', $vendorId)
        ;

        return $conn->fetchOne($select);
    }

    public function updateVendorStatusById($vendorId, $status, $asTransaction=false) {
        if (empty($vendorId)) {
            return;
        }
        $userStatus = ($status == \Omnyfy\Vendor\Api\Data\VendorInterface::STATUS_ENABLED) ? 1 : 0;

        $conn = $this->getConnection();

        $table = $this->getEntityTable();
        $userTable = $conn->getTableName('admin_user');
        $relateTable = $conn->getTableName('omnyfy_vendor_vendor_admin_user');

        if ($asTransaction) {
            $conn->beginTransaction();
        }

        $conn->update($table,
            ['status' => $status],
            ['entity_id=?' => $vendorId]
        );

        $select = $conn->select()
            ->from($relateTable, ['user_id'])
            ->where('vendor_id=?', $vendorId)
        ;

        $conn->update($userTable,
            ['is_active' => $userStatus],
            ['user_id in (?)' => $select]
        );

        if ($asTransaction) {
            $conn->commit();
        }
    }

    public function getVendorNameById($vendorId)
    {
        $vendorId = intval($vendorId);
        if (empty($vendorId)) {
            return false;
        }

        $conn = $this->getConnection();
        $table = $this->getEntityTable();
        $select = $conn->select()
            ->from($table, ['name'])
            ->where('entity_id=?', $vendorId)
        ;

        return $conn->fetchOne($select);
    }

    public function getAllVendorIds($checkStatus=false)
    {
        $conn = $this->getConnection();
        $table = $this->getEntityTable();
        $select = $conn->select()
            ->from($table, ['entity_id']);

        if ($checkStatus) {
            $select->where('status=?', \Omnyfy\Vendor\Api\Data\VendorInterface::STATUS_ENABLED);
        }

        return $conn->fetchCol($select);
    }
}