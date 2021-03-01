<?php

namespace Omnyfy\Mcm\Block\Adminhtml\Items\Column;

use Omnyfy\Vendor\Api\VendorRepositoryInterface;
use Omnyfy\Mcm\Model\ResourceModel\FeesManagement as FeesManagementResource;
use Omnyfy\Mcm\Helper\Data as HelperData;

class CategoryFees extends \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn
{

    protected $vendorRepository;
    
    protected $feesManagementResource;

    protected $_helper;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        FeesManagementResource $feesManagementResource,
        VendorRepositoryInterface $vendorRepository,
		HelperData $helper,
        array $data = [])
    {
        $this->feesManagementResource = $feesManagementResource;
        $this->vendorRepository = $vendorRepository;
		$this->_helper = $helper;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
    }

    public function getCategoryFees($item) {
        $itemId = $item->getItemId();
        $categoryFees = $this->feesManagementResource->getCategoryFeeByItemId($itemId);
        /* $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $currencyCode = $storeManager->getStore()->getCurrentCurrencyCode();
        $currency = $objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($currencyCode);
        $currencySymbol = $currency->getCurrencySymbol(); */
        try {
			return $this->currency($categoryFees);
            /* if (!empty($categoryFees)) {
                return $currencySymbol . $categoryFees;
            } else {
                return $currencySymbol . '0.00';
            } */
        } catch (\Exception $e) {
            $this->_logger->critical($e->getMessage());
        }
    }
	
	public function currency($value) {
        return $this->_helper->formatToBaseCurrency($value);
    }
}