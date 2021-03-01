<?php

namespace Omnyfy\Mcm\Model\ResourceModel;

class MarketplaceDetailedReport extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb 
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
        $this->_init('omnyfy_mcm_marketplace_fee_report_admin', 'id');
    }
    
    /**
     * @param $marketplaceReportorderData
     */
    public function marketplacereportOrdersData($marketplaceReportorderData){
        $this->saveToTable('omnyfy_mcm_marketplace_fee_report_admin', $marketplaceReportorderData);

    }
    /**
     * @param $data
     */
    public function marketplacereportOrdersItem($marketplaceReport){
        $this->saveToTable('omnyfy_mcm_marketplace_fee_report_admin', $marketplaceReport);
    }
    
    /**
     * @param $orderData
     */
    public function updateMarketplacereportOrdersData($updateMarketplaceReportData, $orderTotal){
        $conn = $this->getConnection();
        $select = "UPDATE omnyfy_mcm_marketplace_fee_report_admin SET disbursement_fee='".$updateMarketplaceReportData['disbursement_fee']."', transaction_fees='".$updateMarketplaceReportData['transaction_fees']."', gross_earnings='".$updateMarketplaceReportData['gross_earnings']."'  WHERE order_total_value='" . $orderTotal . "'";
        $result = $conn->exec($select);
        return $result;
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
