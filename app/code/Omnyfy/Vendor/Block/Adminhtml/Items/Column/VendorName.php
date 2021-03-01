<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 17/7/17
 * Time: 4:23 PM
 */

namespace Omnyfy\Vendor\Block\Adminhtml\Items\Column;

use Omnyfy\Vendor\Api\VendorRepositoryInterface;

class VendorName extends \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn
{
    protected $vendorResource;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource,
        array $data = [])
    {
        $this->vendorResource = $vendorResource;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
    }

    public function getVendorName($item)
    {
        $vendorId = $item->getVendorId();
        $vendorName = $this->vendorResource->getVendorNameById($vendorId);
        return empty($vendorName) ? '' : $vendorName;
    }
}