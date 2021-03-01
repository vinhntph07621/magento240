<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\MarketplaceDetailedReport;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Export extends \Magento\Framework\App\Action\Action {

    protected $fileFactory;
    protected $csvProcessor;
    protected $directoryList;

    /**
     * @var \Omnyfy\Mcm\Model\MarketplaceDetailedReportFactory
     */
    protected $_marketplaceReportFactory;
    
    /**
     * @var \Omnyfy\Mcm\Model\ResourceModel\MarketplaceDetailedReport
     */
    protected $_marketplaceReportResource;
    
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;
    
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_adminSession;
    
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Omnyfy\Mcm\Model\ResourceModel\MarketplaceDetailedReport $_marketplaceReportResource
     * @param \Omnyfy\Mcm\Model\MarketplaceDetailedReportFactory $_marketplaceReportFactory
     * @param \Magento\Backend\Model\Auth\Session $adminSession
     * @param PriceCurrencyInterface $priceFormatter
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
    	\Magento\Framework\App\Response\Http\FileFactory $fileFactory,
    	\Magento\Framework\File\Csv $csvProcessor, 
        \Omnyfy\Mcm\Model\ResourceModel\MarketplaceDetailedReport $_marketplaceReportResource,
        \Omnyfy\Mcm\Model\MarketplaceDetailedReportFactory $_marketplaceReportFactory,    
        PriceCurrencyInterface $priceFormatter, 
        \Magento\Backend\Model\Auth\Session $adminSession,    
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
	)
	{
    	$this->fileFactory = $fileFactory;
    	$this->csvProcessor = $csvProcessor;
    	$this->directoryList = $directoryList;
        $this->_marketplaceReportResource = $_marketplaceReportResource;
        $this->_marketplaceReportFactory = $_marketplaceReportFactory;
        $this->_adminSession = $adminSession;
        $this->priceFormatter = $priceFormatter;
    	parent::__construct($context);
	}
 
	public function execute()
	{
            
        $date = date("d-m-Y");
    	$fileName = 'MarketplaceFeesDetailedEarningsReport'.$date.'.csv';
        
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
        
        $marketplaceReport = $this->_marketplaceReportFactory->create();
	$collection = $marketplaceReport->getCollection();
        $marketplaceReportData = $collection->getData();

    	$result[] = [
            'Order ID',
            'Vendor ID',
            'Vendor Name',
            'Product SKU',
            'Product Name',
            'Product Price',
            'Shipping and Handling Total',
            'Discount',
            'Order Total Value',
            'Category Commission',
            'Seller Fee',
            'Disbursement Fee',
            'Transaction Fee',
            'Gross Earnings'
        ];

    	foreach ($marketplaceReportData as $marketplaceReport) {
        
        $currencyCode = isset($mcm['base_currency_code']) ? $mcm['base_currency_code'] : null;
        
        $result[] = [
            	$marketplaceReport['order_id'],
            	$marketplaceReport['vendor_id'],
                $marketplaceReport['vendor_name'],
                $marketplaceReport['product_sku'],
                $marketplaceReport['product_name'],
                $marketplaceReport['price_paid'] ? $this->priceFormatter->format($marketplaceReport['price_paid'], false, null, null, $currencyCode) : '' ,           
            	$marketplaceReport['shipping_and_hanldling_total'] ? $this->priceFormatter->format($marketplaceReport['shipping_and_hanldling_total'], false, null, null, $currencyCode) : '' ,
                $marketplaceReport['discount'] ? $this->priceFormatter->format($marketplaceReport['discount'], false, null, null, $currencyCode) : '' ,
                $marketplaceReport['order_total_value'] ? $this->priceFormatter->format($marketplaceReport['order_total_value'], false, null, null, $currencyCode) : '' ,
                $marketplaceReport['category_commission'] ? $this->priceFormatter->format($marketplaceReport['category_commission'], false, null, null, $currencyCode) : '' ,
                $marketplaceReport['seller_fee'] ? $this->priceFormatter->format($marketplaceReport['seller_fee'], false, null, null, $currencyCode) : '' ,
                $marketplaceReport['disbursement_fee'] ? $this->priceFormatter->format($marketplaceReport['disbursement_fee'], false, null, null, $currencyCode) : '' ,
                $marketplaceReport['transaction_fees'] ? $this->priceFormatter->format($marketplaceReport['transaction_fees'], false, null, null, $currencyCode) : '' ,
                $marketplaceReport['gross_earnings'] ? $this->priceFormatter->format($marketplaceReport['gross_earnings'], false, null, null, $currencyCode) : '' 
        	];	
    	}
  
    	return $result;
	}
}