<?php

namespace Omnyfy\Mcm\Block\Adminhtml\Items\Column;

class TaxOnFees extends \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn
{

    /**
     * @var \Omnyfy\Mcm\Model\ResourceModel\FeesManagement
     */
    protected $feesManagementResource;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        \Omnyfy\Mcm\Model\ResourceModel\FeesManagement $feesManagementResource,
        array $data = [])
    {
        $this->feesManagementResource = $feesManagementResource;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
    }

    public function getTaxPercentageOnFees($item) {
        $itemId = $item->getItemId();
        $tax_percentage = $this->feesManagementResource->getTaxPercentageByItemId($itemId);
        if (!empty($tax_percentage)) {
            return $tax_percentage.'%';
        } else {
            return '0%';
        }
    }
}