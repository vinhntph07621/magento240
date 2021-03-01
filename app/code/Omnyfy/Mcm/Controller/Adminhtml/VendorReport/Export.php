<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\VendorReport;

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
     * @var \Omnyfy\Mcm\Model\ResourceModel\VendorFeeReportAdmin
     */
    protected $vendorFeeReportAdminResource;
    
    /**
     * @var \Omnyfy\Mcm\Model\VendorFeeReportAdminFactory
     */
    protected $_mcmVendorFeeReportAdminFactory;
    
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;
    
    /**
     * @var \Omnyfy\Mcm\Model\ResourceModel\FeesManagement
     */
    protected $feesManagementResource;
    
    /**
     * @var \Omnyfy\Mcm\Model\VendorReportVendorFactory
     */
    protected $vendorReportVendorFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Omnyfy\Mcm\Model\VendorFeeReportAdminFactory $mcmvendorFeeReportAdminFactory,
     * @param \Omnyfy\Mcm\Model\ResourceModel\VendorFeeReportAdmin $vendorFeeReportAdminResource,
     * @param \Omnyfy\Mcm\Model\ResourceModel\FeesManagement $feesManagementResource,
     * @param \Omnyfy\Mcm\Model\VendorReportVendorFactory $vendorReportVendorFactory,
     * @param PriceCurrencyInterface $priceFormatter
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
    	\Magento\Framework\App\Response\Http\FileFactory $fileFactory,
    	\Magento\Framework\File\Csv $csvProcessor, 
        \Omnyfy\Mcm\Model\ResourceModel\VendorFeeReportAdmin $vendorFeeReportAdminResource,
        \Omnyfy\Mcm\Model\VendorFeeReportAdminFactory $mcmVendorFeeReportAdminFactory,
        PriceCurrencyInterface $priceFormatter,   
        \Omnyfy\Mcm\Model\VendorFeeReportAdminFactory $_vendorFeeReportFactory,
        \Omnyfy\Mcm\Model\VendorReportVendorFactory $vendorReportVendorFactory,      
        \Omnyfy\Mcm\Model\ResourceModel\FeesManagement $feesManagementResource,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
	)
	{
    	$this->fileFactory = $fileFactory;
    	$this->csvProcessor = $csvProcessor;
    	$this->directoryList = $directoryList;
        $this->vendorFeeReportAdminResource = $vendorFeeReportAdminResource;
        $this->_mcmVendorFeeReportAdminFactory = $mcmVendorFeeReportAdminFactory;
        $this->_vendorReportVendorFactory = $vendorReportVendorFactory;
        $this->feesManagementResource = $feesManagementResource;
        $this->priceFormatter = $priceFormatter;
    	parent::__construct($context);
	}
 
	public function execute()
	{
        $date = date("d-m-Y");
    	$fileName = 'VendorFeesReport'.$date.'.csv';
        
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
 
	protected function getCsvData() {
        $result = [];

        $mcmOrder = $this->_mcmVendorFeeReportAdminFactory->create();
        $collection = $mcmOrder->getCollection();
        $mcmOrderData = $collection->getData();

        $result[] = [
            'Order ID',
            'Product SKU',
            'Product Name',
            'Product Price',
            'Shipping and Handling Total',
            'Discount',
            'Order Total Value',
            'Category Commission',
            'Seller Fee',
            'Disbursement Fee',
            'Total Fees',
            'Gross Earnings',
            'Tax',
            'Net Earnings'
        ];

        foreach ($mcmOrderData as $order) {

            $currencyCode = isset($order['base_currency_code']) ? $order['base_currency_code'] : null;

            $result[] = [
                $order['order_id'],
                $order['product_sku'],
                $order['product_name'],
                $order['price_paid'] ? $this->priceFormatter->format($order['price_paid'], false, null, null, $currencyCode) : '',
                $order['shipping_and_hanldling_total'] ? $this->priceFormatter->format($order['shipping_and_hanldling_total'], false, null, null, $currencyCode) : '',
                $order['discount'] ? $this->priceFormatter->format($order['discount'], false, null, null, $currencyCode) : '',
                $order['order_total_value'] ? $this->priceFormatter->format($order['order_total_value'], false, null, null, $currencyCode) : '',
                $order['category_commission'] ? $this->priceFormatter->format($order['category_commission'], false, null, null, $currencyCode) : '',
                $order['seller_fee'] ? $this->priceFormatter->format($order['seller_fee'], false, null, null, $currencyCode) : '',
                $order['disbursement_fee'] ? $this->priceFormatter->format($order['disbursement_fee'], false, null, null, $currencyCode) : '',
                $order['total_fee'] ? $this->priceFormatter->format($order['total_fee'], false, null, null, $currencyCode) : '',
                $order['gross_earnings'] ? $this->priceFormatter->format($order['gross_earnings'], false, null, null, $currencyCode) : '',
                $order['tax'] ? $this->priceFormatter->format($order['tax'], false, null, null, $currencyCode) : '',
                $order['net_earnings'] ? $this->priceFormatter->format($order['net_earnings'], false, null, null, $currencyCode) : ''
            ];
        }
        return $result;
    }
}