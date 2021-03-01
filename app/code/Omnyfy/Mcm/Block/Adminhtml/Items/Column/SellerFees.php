<?php

namespace Omnyfy\Mcm\Block\Adminhtml\Items\Column;

use Omnyfy\Mcm\Model\ResourceModel\FeesManagement as FeesManagementResource;
use Omnyfy\Mcm\Helper\Data as HelperData;

class SellerFees extends \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn
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
		HelperData $helper,
        array $data = [])
    {
        $this->feesManagementResource = $feesManagementResource;
		$this->_helper = $helper;
		
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
    }

    public function getSellerFees($item) {
        $itemId = $item->getItemId();
        $sellerFees = $this->feesManagementResource->getSellerFeeByItemId($itemId);

        try {
			return $this->currency($sellerFees);
        } catch (\Exception $e) {
            $this->_logger->critical($e->getMessage());
        }
    }

    public function getBaseSellerFees($item) {
        $itemId = $item->getItemId();
        return $this->_helper->getSellerFeeByItemId($itemId);
    }

	public function currency($value) {
        return $this->_helper->formatToBaseCurrency($value);
    }

    public function getBaseToOrderRate() {
        return $this->getOrder()->getBaseToOrderRate();
    }
}