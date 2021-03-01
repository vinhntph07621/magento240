<?php

namespace Omnyfy\Mcm\Model\ResourceModel;

class VendorFeeReportAdmin extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb 
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Construct.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string|null                                       $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
    }

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('omnyfy_mcm_vendor_fee_report_admin', 'id');
    }
    
    /**
     * @param $data
     */
    public function reportOrdersItem($data){
        $this->saveToTable('omnyfy_mcm_vendor_fee_report_admin', $data);
    }
    
    /**
     * @param $orderData
     */
    public function reportOrdersData($orderData){
        $this->saveToTable('omnyfy_mcm_vendor_fee_report_admin', $orderData);
    }
    
    /**
     * @param $data
     */
    public function adminReportOrdersData($orderData){
        $this->saveToTable('omnyfy_mcm_vendor_fee_report_admin', $orderData);
    }
    
    public function getCategoryFeeTaxByItemId($itemId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_mcm_vendor_order_item');

        $select = $conn->select()->from($table,['category_fee_tax'])->where("order_item_id = ?",$itemId);

        return $conn->fetchOne($select);
    }
    
    public function getSellerFeeTaxByItemId($itemId)
    {
        $conn = $this->getConnection();

        $table = $conn->getTableName('omnyfy_mcm_vendor_order_item');

        $select = $conn->select()->from($table,['seller_fee_tax'])->where("order_item_id = ?",$itemId);

        return $conn->fetchOne($select);
    }
    
    /**
     * @param $orderData
     */
    public function updateReportOrdersData($updateOrderData, $orderTotal){
        $conn = $this->getConnection();
        $select = "UPDATE omnyfy_mcm_vendor_fee_report_admin SET disbursement_fee='" . $updateOrderData['disbursement_fee'] . "', total_fee='" . $updateOrderData['total_fee'] . "', gross_earnings='" . $updateOrderData['gross_earnings'] . "', tax='" . $updateOrderData['tax'] . "', net_earnings='" . $updateOrderData['net_earnings'] . "'  WHERE order_total_value='" . $orderTotal . "'";
        $result = $conn->exec($select);
        return $result;
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