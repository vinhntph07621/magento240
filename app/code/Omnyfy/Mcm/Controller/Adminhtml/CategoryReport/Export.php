<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\CategoryReport;

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
     * @var \Omnyfy\Mcm\Model\CategoryCommissionReportFactory
     */
    protected $_categoryCommissionReportFactory;
    
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;
    
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Omnyfy\Mcm\Model\CategoryCommissionReportFactory $categoryCommissionReportFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceFormatter
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
    	\Magento\Framework\App\Response\Http\FileFactory $fileFactory,
    	\Magento\Framework\File\Csv $csvProcessor,   
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceFormatter,   
        \Omnyfy\Mcm\Model\CategoryCommissionReportFactory $categoryCommissionReportFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
	)
	{
    	$this->fileFactory = $fileFactory;
    	$this->csvProcessor = $csvProcessor;
    	$this->directoryList = $directoryList;
        $this->_categoryCommissionReportFactory = $categoryCommissionReportFactory;
        $this->priceFormatter = $priceFormatter;
    	parent::__construct($context);
	}
 
	public function execute()
	{
        $date = date("d-m-Y");
    	$fileName = 'CategoryCommissionReport'.$date.'.csv';
        
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
        
        $categoryCommissionReport = $this->_categoryCommissionReportFactory->create();
	$collection = $categoryCommissionReport->getCollection();
        $categoryCommissionReportData = $collection->getData();

    	$result[] = [
            'Category Name',
            'Category Commission Percentage',
            'Fees Earned'
        ];

        foreach ($categoryCommissionReportData as $data){
        
            $currencyCode = isset($data['base_currency_code']) ? $data['base_currency_code'] : null;
 
            $result[] = [
            	$data['category_name'],
            	$data['category_commission_percentage'].'%',
                $data['category_commission_earned'] ? $this->priceFormatter->format($data['category_commission_earned'], false, null, null, $currencyCode) : '' 
        	];

    	}
    	return $result;
	}
}