<?php

namespace Omnyfy\Mcm\Model\ResourceModel;

class VendorFeeReport extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb 
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
        $this->_init('omnyfy_vendor_vendor_entity', 'id');
    }
    
    public function getTotalDisbursementFeeByVendorId($vendorId)
    {
        $conn = $this->getConnection();
        
        $select = "SELECT (SUM(disbursement_fee)+SUM(disbursement_fee_tax)) FROM omnyfy_mcm_vendor_order WHERE vendor_id = '".$vendorId."' ";
        
        return $conn->fetchOne($select);
    }
    
    public function getTotalCategoryFeeByVendorId($vendorId)
    {
        $conn = $this->getConnection();

        $select = "SELECT SUM(category_fee) FROM omnyfy_mcm_vendor_order_item WHERE vendor_id = '".$vendorId."' ";
        
        return $conn->fetchOne($select);
    }
    
    public function getTotalCategoryTaxByVendorId($vendorId)
    {
        $conn = $this->getConnection();
        
        $select = "SELECT SUM(category_fee_tax) FROM omnyfy_mcm_vendor_order_item WHERE vendor_id = '".$vendorId."' ";
        
        return $conn->fetchOne($select);
    }
    
    public function getTotalSellerFeeByVendorId($vendorId)
    {
        $conn = $this->getConnection();

        $select = "SELECT SUM(seller_fee) FROM omnyfy_mcm_vendor_order_item WHERE vendor_id = '".$vendorId."' ";
        
        return $conn->fetchOne($select);
    }
    
    public function getTotalSellerTaxByVendorId($vendorId)
    {
        $conn = $this->getConnection();
        
        $select = "SELECT SUM(seller_fee_tax) FROM omnyfy_mcm_vendor_order_item WHERE vendor_id = '".$vendorId."' ";
        
        return $conn->fetchOne($select);
    }
}