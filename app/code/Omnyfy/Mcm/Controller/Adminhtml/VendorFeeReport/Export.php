<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\VendorFeeReport;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Export extends \Magento\Framework\App\Action\Action {

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;
    
    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;
    
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var Omnyfy\Mcm\Model\ResourceModel\VendorFeeReport
     */
    protected $vendorFeeReportResource;
    
    /**
     * @var \Omnyfy\Mcm\Model\VendorFeeReportFactory
     */
    protected $_mcmvendorFactory;
    
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Omnyfy\Mcm\Model\VendorFeeReportFactory $mcmvendorFactory,
     * @param PriceCurrencyInterface $priceFormatter
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
    	\Magento\Framework\App\Response\Http\FileFactory $fileFactory,
    	\Magento\Framework\File\Csv $csvProcessor, 
        \Omnyfy\Mcm\Model\ResourceModel\VendorFeeReport $vendorFeeReportResource,
        \Omnyfy\Mcm\Model\VendorFeeReportFactory $mcmvendorFactory,    
        PriceCurrencyInterface $priceFormatter,   
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
	)
	{
    	$this->fileFactory = $fileFactory;
    	$this->csvProcessor = $csvProcessor;
    	$this->directoryList = $directoryList;
        $this->vendorFeeReportResource = $vendorFeeReportResource;
        $this->_mcmvendorFactory = $mcmvendorFactory;
        $this->priceFormatter = $priceFormatter;
    	parent::__construct($context);
	}
 
	public function execute()
	{
        $date = date("d-m-Y");
    	$fileName = 'MarketplaceFeesSummaryEarningsReport-byvendor'.$date.'.csv';
        
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
        
        $mcmVendor = $this->_mcmvendorFactory->create();
	$collection = $mcmVendor->getCollection();
        $mcmVendorData = $collection->getData();
        
    	$result[] = [
            'Vendor ID',
            'Vendor Name',
            'Total Category Commission',
            'Total Seller Fee',
            'Total Disbursement Fee',
            'Gross Earnings'
        ];

    	foreach ($mcmVendorData as $vendor) {
            
            $id = $vendor['entity_id'];
            $category_commission_total = $this->vendorFeeReportResource->getTotalCategoryFeeByVendorId($id);
            $seller_fee_total = $this->vendorFeeReportResource->getTotalSellerFeeByVendorId($id);
            $disbursementFee = $this->vendorFeeReportResource->getTotalDisbursementFeeByVendorId($id);
            $categoryCommissionTax = $this->vendorFeeReportResource->getTotalCategoryTaxByVendorId($id);
            $sellerFeeTax = $this->vendorFeeReportResource->getTotalSellerTaxByVendorId($id);

            $categoryFee = $category_commission_total + $categoryCommissionTax;
            $sellerFee = $seller_fee_total + $sellerFeeTax;
            $totalFeesOnVendors = $categoryFee + $sellerFee + $disbursementFee;
            
        $currencyCode = isset($order['base_currency_code']) ? $order['base_currency_code'] : null;
        
        $result[] = [
            	$vendor['entity_id'],
            	$vendor['name'],
            	$this->priceFormatter->format($categoryFee, false, null, null, $currencyCode),
            	$this->priceFormatter->format($sellerFee, false, null, null, $currencyCode),
                $this->priceFormatter->format($disbursementFee, false, null, null, $currencyCode),
                $this->priceFormatter->format($totalFeesOnVendors, false, null, null, $currencyCode),
        	];
        
            
    	}
    	return $result;
	}
}