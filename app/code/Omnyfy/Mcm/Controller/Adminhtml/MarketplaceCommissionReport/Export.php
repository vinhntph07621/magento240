<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\MarketplaceCommissionReport;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Export extends \Magento\Framework\App\Action\Action {

    protected $fileFactory;
    protected $csvProcessor;
    protected $directoryList;

    
    /**
     * @var \Omnyfy\Mcm\Model\ResourceModel\FeesManagement
     */
    protected $feesManagementResource;
    
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_orders;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Omnyfy\Mcm\Model\ResourceModel\FeesManagement $feesManagementResource
     * @param \Magento\Sales\Model\Order $orders
     * @param PriceCurrencyInterface $priceFormatter
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
    	\Magento\Framework\App\Response\Http\FileFactory $fileFactory,
    	\Magento\Framework\File\Csv $csvProcessor, 
        \Omnyfy\Mcm\Model\ResourceModel\FeesManagement $feesManagementResource,
        PriceCurrencyInterface $priceFormatter,
        \Magento\Sales\Model\Order $orders,    
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
	)
	{
    	$this->fileFactory = $fileFactory;
    	$this->csvProcessor = $csvProcessor;
    	$this->directoryList = $directoryList;
        $this->feesManagementResource = $feesManagementResource;
        $this->priceFormatter = $priceFormatter;
        $this->_orders = $orders;
    	parent::__construct($context);
	}
 
	public function execute()
	{
        $date = date("d-m-Y");
    	$fileName = 'MarketplaceFeesSummaryEarningsReport-byorder'.$date.'.csv';
        
    	$filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR). "/" . $fileName;

    	$feeData = $this->getCsvData();
        
    	$this->csvProcessor
    	    ->setDelimiter(',')
//        	->setEnclosure(' ')
        	->saveData(
            	$filePath,
            	$feeData
        	);
 
    	return $this->fileFactory->create(
        	$fileName,
        	[
            	'type' => "filename",
            	'value' => $fileName,
            	'rm' => true,
        	],
            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
        	'application/octet-stream'
    	);
	}
 
	protected function getCsvData( )
	{
    	$result = [];
        
        $collection = $this->_orders->getCollection();
        $orderData = $collection->getData();
        
    	$result[] = [
            'ID',
            'Order ID',
            'Date and Time',
            'Shipping and Handling Total',
            'Discount',
            'Order Total Value',
            'Category Commission',
            'Seller Fee',
            'Disbursement Fee',
            'Transaction Surcharge',
            'Gross Earnings'
        ];

    	foreach ($orderData as $order) {
            
            $seller_fee_total = $this->feesManagementResource->getTotalSellerFeeByOrderId($order['entity_id']);
            $category_commission_total = $this->feesManagementResource->getTotalCategoryFeeByOrderId($order['entity_id']);
            $disbursement_fee = $this->feesManagementResource->getDisbursementFeeByOrderId($order['entity_id']);
            $sellerFeeTax = $this->feesManagementResource->getTotalSellerTaxByOrderId($order['entity_id']);
            $categoryCommissionTax = $this->feesManagementResource->getTotalCategoryTaxByOrderId($order['entity_id']);
            $disbursementFeeTax = $this->feesManagementResource->getDisbursementTaxByOrderId($order['entity_id']);
                     
            $categoryFee = $category_commission_total + $categoryCommissionTax;
            $sellerFee = $seller_fee_total + $sellerFeeTax;
            $disbursementFee = $disbursement_fee + $disbursementFeeTax;
            $totalFeesOnVendors = $categoryFee + $sellerFee + $disbursementFee;
            $moEarning = $order['mcm_transaction_fee_incl_tax'] + $totalFeesOnVendors;
            
        $currencyCode = isset($order['base_currency_code']) ? $order['base_currency_code'] : null;
        
        $result[] = [
            	$order['entity_id'],
            	$order['increment_id'],
            	$order['created_at'],
            	$this->priceFormatter->format($order['shipping_incl_tax'], false, null, null, $currencyCode),
            	$this->priceFormatter->format($order['discount_amount'], false, null, null, $currencyCode),
            	$this->priceFormatter->format($order['grand_total'], false, null, null, $currencyCode),
            	$this->priceFormatter->format($categoryFee, false, null, null, $currencyCode),
            	$this->priceFormatter->format($sellerFee, false, null, null, $currencyCode),
                $this->priceFormatter->format($disbursementFee, false, null, null, $currencyCode),
                $this->priceFormatter->format($order['mcm_transaction_fee_incl_tax'], false, null, null, $currencyCode),
                $this->priceFormatter->format($moEarning, false, null, null, $currencyCode),
        	];
        
    	}
  
    	return $result;
	}
}