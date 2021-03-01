<?php

namespace Omnyfy\Mcm\Model\ResourceModel;

use Magento\Eav\Model\Entity\AbstractEntity;

class FeesManagement extends AbstractEntity
{
    /**
     * @param $mcmorderItem
     */
    public function saveMcmOrderItemRelation($mcmorderItem){
        $this->saveToTable('omnyfy_mcm_vendor_order_item', $mcmorderItem);
    }
    
    /**
     * @param $data
     */
    public function reportOrdersItem($data){
        $this->saveToTable('omnyfy_mcm_vendor_fee_report_admin', $data);
    }
    
    /**
     * @param $data
     */
    public function getTotalFeesTaxOnOrder($data){
        $this->saveToTable('omnyfy_mcm_vendor_fee_report_admin', $data);
    }
    
    /**
     * @param $mcmVendorOrder
     */
    public function saveMcmVendorOrderRelation($mcmVendorOrder){
        $this->saveToTable('omnyfy_mcm_vendor_order', $mcmVendorOrder);
    }

    public function getVendorOrderItem($itemId) {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_mcm_vendor_order_item');

        $select = $conn->select()->from($table)->where("order_item_id = ?",$itemId);

        return $conn->fetchRow($select);
    }

    public function getCategoryFeeByItemId($itemId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_mcm_vendor_order_item');

        $select = $conn->select()->from($table,['category_fee'])->where("order_item_id = ?",$itemId);

        return $conn->fetchOne($select);
    }

    public function getCategoryRateByItemId($itemId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_mcm_vendor_order_item');

        $select = $conn->select()->from($table,['category_commission_percentage'])->where("order_item_id = ?",$itemId);

        return $conn->fetchOne($select);
    }
    
    public function getSellerFeeByItemId($itemId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_mcm_vendor_order_item');

        $select = $conn->select()->from($table,['seller_fee'])->where("order_item_id = ?",$itemId);

        return $conn->fetchOne($select);
    }
    
    public function getTaxPercentageByItemId($itemId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_mcm_vendor_order_item');

        $select = $conn->select()->from($table,['tax_percentage'])->where("order_item_id = ?",$itemId);

        return $conn->fetchOne($select);
    }
    
    public function getTotalCategoryFeeByOrderId($orderId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_mcm_vendor_order');
        
        $select = "SELECT SUM(total_category_fee) FROM omnyfy_mcm_vendor_order WHERE order_id = '".$orderId."' ";
        
        return $conn->fetchOne($select);
    }
    
    public function getTotalCategoryTaxByOrderId($orderId)
    {
        $conn = $this->getConnection();
        
        $select = "SELECT SUM(category_fee_tax) FROM omnyfy_mcm_vendor_order_item WHERE order_id = '".$orderId."' ";
        
        return $conn->fetchOne($select);
    }
    
    public function getTotalSellerFeeByOrderId($orderId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_mcm_vendor_order');

        $select = "SELECT SUM(total_seller_fee) FROM omnyfy_mcm_vendor_order WHERE order_id = '".$orderId."' ";
        
        return $conn->fetchOne($select);
    }
    
    public function getTotalSellerTaxByOrderId($orderId)
    {
        $conn = $this->getConnection();
        
        $select = "SELECT SUM(seller_fee_tax) FROM omnyfy_mcm_vendor_order_item WHERE order_id = '".$orderId."' ";
        
        return $conn->fetchOne($select);
    }
    
    public function getDisbursementFeeByOrderId($orderId)
    {
        $conn = $this->getConnection();

        $select = "SELECT SUM(disbursement_fee) FROM omnyfy_mcm_vendor_order WHERE order_id = '".$orderId."' ";
        
        return $conn->fetchOne($select);
    }
    
    public function getDisbursementTaxByOrderId($orderId)
    {
        $conn = $this->getConnection();

        $select = "SELECT SUM(disbursement_fee_tax) FROM omnyfy_mcm_vendor_order WHERE order_id = '".$orderId."' ";
        
        return $conn->fetchOne($select);
    }
    
    public function getTaxOnFeesByOrderId($orderId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_mcm_vendor_order');

        $select = "SELECT SUM(total_tax_onfees) FROM omnyfy_mcm_vendor_order WHERE order_id = '".$orderId."' ";
        
        return $conn->fetchOne($select);
    }
    
    public function getSellerFeeByVendorId($vendorId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_mcm_fees_and_charges');

        $select = $conn->select()->from($table,['seller_fee'])->where("vendor_id = ?",$vendorId);

        return $conn->fetchOne($select);
    }
    
    public function getVendorTaxRateByVendorId($vendorId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_mcm_fees_and_charges');

        $select = $conn->select()->from($table,['tax_rate'])->where("vendor_id = ?",$vendorId);

        return $conn->fetchOne($select);
    }
    
    public function isVendorFeeActive($vendorId){
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_mcm_fees_and_charges');

        $select = $conn->select()->from($table,['status'])->where("vendor_id = ?",$vendorId);

        $result = $conn->fetchOne($select);
        if($result == 1){
            return true;
        }else{
            return false;
        }
    }
    
    public function getSellerMinFeeByVendorId($vendorId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_mcm_fees_and_charges');

        $select = $conn->select()->from($table, ['min_seller_fee'])->where("vendor_id = ?", $vendorId);

        return $conn->fetchOne($select);
    }
    
    public function getSellerMaxFeeByVendorId($vendorId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_mcm_fees_and_charges');

        $select = $conn->select()->from($table,['max_seller_fee'])->where("vendor_id = ?",$vendorId);

        return $conn->fetchOne($select);
    }
    
    public function getVendorNameByVendorId($vendorId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_vendor_vendor_entity');

        $select = $conn->select()->from($table,['name'])->where("entity_id = ?",$vendorId);

        return $conn->fetchOne($select);
    }
    
    public function getDisbursmentFeeByVendorId($vendorId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_mcm_fees_and_charges');

        $select = $conn->select()->from($table,['disbursement_fee'])->where("vendor_id = ?",$vendorId);

        return $conn->fetchOne($select);
    }
    
    public function getTotalCategoryFee($ordervendorId, $orderId){
        $conn = $this->getConnection();

        $select = "SELECT SUM(category_fee) FROM omnyfy_mcm_vendor_order_item WHERE order_id = '".$orderId."' AND vendor_id ='".$ordervendorId."'";
        
        return $conn->fetchOne($select);
    }
    
    public function getVendorItemsTotals($orderVendorId, $orderId) {
        $adapter = $this->getConnection();
        $table = $this->getTable('sales_order_item');
        $select = $adapter->select()->from(
                        $table, [
                            'vendor_id', 
                            'order_id',
                            'row_total' => 'SUM(row_total)',
                            'base_row_total' => 'SUM(base_row_total)', 
                            'row_total_incl_tax' => 'SUM(row_total_incl_tax)',
                            'base_row_total_incl_tax' => 'SUM(base_row_total_incl_tax)',
                            'tax_amount' => 'SUM(tax_amount)',
                            'base_tax_amount' => 'SUM(base_tax_amount)',
                            'discount_amount' => 'SUM(discount_amount)',
                            'base_discount_amount' => 'SUM(base_discount_amount)'
                            ]
                )->where(
                        "vendor_id = ?", (int) $orderVendorId
                )->where(
                "order_id = ?", (int) $orderId
                )->where('product_type != ?', "mvcp");

        return $adapter->fetchRow($select);
    }
    
    public function getTotalSellerFee($ordervendorId, $orderId){
        $conn = $this->getConnection();

        $select = "SELECT SUM(seller_fee) FROM omnyfy_mcm_vendor_order_item WHERE order_id = '".$orderId."' AND vendor_id ='".$ordervendorId."'";
        
        return $conn->fetchOne($select);
    }
   
    public function getTotalTaxOnFees($ordervendorId, $orderId){
        $conn = $this->getConnection();

        $select = "SELECT SUM(tax_amount) FROM omnyfy_mcm_vendor_order_item WHERE order_id = '".$orderId."' AND vendor_id ='".$ordervendorId."'";
        
        return $conn->fetchOne($select);
    }
    
    public function getVendorByUserId($userId){
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_vendor_vendor_admin_user');

        $select = $conn->select()->from($table,['vendor_id'])->where("user_id = ?",$userId);
        
        return $conn->fetchOne($select);
    }
    
    public function getVendorSellerFee($orderId, $vendorId){
        $conn = $this->getConnection();

        $select = "SELECT total_seller_fee FROM omnyfy_mcm_vendor_order WHERE order_id = '".$orderId."' AND vendor_id ='".$vendorId."'";
        
        return $conn->fetchOne($select);
    }
    
    public function getVendorCategoryFee($orderId, $vendorId){
        $conn = $this->getConnection();

        $select = "SELECT total_category_fee FROM omnyfy_mcm_vendor_order WHERE order_id = '".$orderId."' AND vendor_id ='".$vendorId."'";
        
        return $conn->fetchOne($select);
    }
    
    public function getVendorDisbursementFee($orderId, $vendorId){
        $conn = $this->getConnection();

        $select = "SELECT disbursement_fee FROM omnyfy_mcm_vendor_order WHERE order_id = '".$orderId."' AND vendor_id ='".$vendorId."'";
        
        return $conn->fetchOne($select);
    }
    
    public function getVendorTaxOnFees($orderId, $vendorId){
        $conn = $this->getConnection();

        $select = "SELECT total_tax_onfees FROM omnyfy_mcm_vendor_order WHERE order_id = '".$orderId."' AND vendor_id ='".$vendorId."'";
        
        return $conn->fetchOne($select);
    }
    
    public function getVendorOrderTotals($vendorId, $orderId) {
        $adapter = $this->getConnection();
        $table = $this->getTable('omnyfy_mcm_vendor_order');
        $select = $adapter->select()->from(
                        $table, [
                            'vendor_id', 
                            'order_id',
                            'subtotal',
                            'base_subtotal',
                            'subtotal_incl_tax',
                            'base_subtotal_incl_tax',
                            'tax_amount',
                            'base_tax_amount',
                            'discount_amount',
                            'base_discount_amount',
                            'shipping_amount',
                            'base_shipping_amount',
                            'shipping_incl_tax',
                            'base_shipping_incl_tax',
                            'shipping_tax',
                            'base_shipping_tax',
                            'shipping_discount_amount',
                            'grand_total',
                            'base_grand_total'
                            ]
                )->where(
                        "vendor_id = ?", (int) $vendorId
                )->where(
                "order_id = ?", (int) $orderId
        );

        return $adapter->fetchRow($select);
    }
    
    /**
     * @param $updateMcmVendorOrder
     */
    public function updateMcmVendorOrderRelation($updateMcmVendorOrder){
        $conn = $this->getConnection();
        $select = "UPDATE omnyfy_mcm_vendor_order SET shipping_amount='".$updateMcmVendorOrder['shipping_amount']."', base_shipping_amount='".$updateMcmVendorOrder['base_shipping_amount']."', shipping_incl_tax='".$updateMcmVendorOrder['shipping_incl_tax']."' , base_shipping_incl_tax='".$updateMcmVendorOrder['base_shipping_incl_tax']."' , shipping_tax='".$updateMcmVendorOrder['shipping_tax']."' , base_shipping_tax='".$updateMcmVendorOrder['base_shipping_tax']."' , shipping_discount_amount='".$updateMcmVendorOrder['shipping_discount_amount']."'  WHERE vendor_id='" . $updateMcmVendorOrder['vendor_id'] . "' AND order_id='" . $updateMcmVendorOrder['order_id'] . "'";
        $result = $conn->exec($select);
        return $result;
    }
    
    public function getVendorNosOnOrder($orderId){
        $conn = $this->getConnection();

        $select = "SELECT COUNT(DISTINCT vendor_id) FROM sales_order_item WHERE order_id = '".$orderId."' ";
       
        return $conn->fetchOne($select);
    }
    
     public function getVendorIdsOnOrder($orderId){
        $conn = $this->getConnection();

        $select = "SELECT DISTINCT vendor_id FROM sales_order_item WHERE order_id = '".$orderId."' ";
       
        return $conn->fetchOne($select);
    }    
    
    public function getQtyForVendor($orderId,$vendorId){
        $conn = $this->getConnection();

        $select = "SELECT SUM(qty_ordered) FROM sales_order_item WHERE order_id = '".$orderId."' AND vendor_id = '".$vendorId."' ";
       
        return $conn->fetchOne($select);
    }
    
    public function getGrandTotal($orderId,$vendorId){
        $conn = $this->getConnection();

        $select = "SELECT grand_total FROM omnyfy_mcm_vendor_order WHERE order_id = '".$orderId."' AND vendor_id = '".$vendorId."' ";
       
        return $conn->fetchOne($select);
    }
    
    public function saveVendorInvoiceTotals($data){
        $this->saveToTable('omnyfy_mcm_vendor_invoice', $data);
    }
      
    public function getVendorInvoiceTotals($vendorId, $invoiceId) {
        $adapter = $this->getConnection();
        $table = $this->getTable('omnyfy_mcm_vendor_invoice');
        $select = $adapter->select()->from(
                        $table, [
                            'vendor_id', 
                            'order_id',
                            'subtotal',
                            'subtotal_incl_tax',
                            'tax_amount',
                            'discount_amount',
                            'shipping_amount',
                            'shipping_incl_tax',
                            'shipping_tax',
                            'shipping_discount_amount',
                            'grand_total',
                            'base_grand_total'
                            ]
                )->where(
                        "vendor_id = ?", (int) $vendorId
                )->where(
                "invoice_id = ?", (int) $invoiceId
        );

        return $adapter->fetchRow($select);
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
}