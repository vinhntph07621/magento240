<?php

namespace Omnyfy\Mcm\Block\Adminhtml\Items\Column;

use Omnyfy\Vendor\Api\VendorRepositoryInterface;
use Omnyfy\Mcm\Model\ResourceModel\FeesManagement as FeesManagementResource;

class CategoryRate extends \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn
{

    protected $feesManagementResource;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        FeesManagementResource $feesManagementResource,
        array $data = [])
    {
        $this->feesManagementResource = $feesManagementResource;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
    }

    public function getCategoryRate($item) {
        $itemId = $item->getItemId();
        $categoryRate = $this->feesManagementResource->getCategoryRateByItemId($itemId);
        try {
            if (!empty($categoryRate)) {
                return $categoryRate.'%';
            } else {
                return '0%';
            }
        } catch (\Exception $e) {
            $this->_logger->critical($e->getMessage());
        }
    }
}