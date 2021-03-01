<?php
/**
 * Project: Omnyfy Multi Vendor.
 * User: jing
 * Date: 26/4/17
 * Time: 4:17 PM
 */

namespace Omnyfy\Vendor\Plugin;

class StockRegistryProvider
{
    protected $helper;

    protected $_moVendorIds;

    protected $state;

    protected $notCheckTypes = [
        \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE,
        'booking',
    ];

    public function __construct(
        \Omnyfy\Vendor\Helper\Data $helper,
        \Omnyfy\Vendor\Model\Config $config,
        \Magento\Framework\App\State $state
    ) {
        $this->helper = $helper;
        $this->_moVendorIds = $config->getMOVendorIds();
        $this->_state = $state;
    }

    public function aroundGetStockItem(
            \Magento\CatalogInventory\Model\StockRegistryProvider $subject,
            callable $proceed,
            $productId,
            $scopeId
        )
    {
        //2017-09-16 22:39 Jing Xiao,
        //scopeId should be website id, but magento return 0 as default scope id
        //should always check with specified website id.
        //$scopeId = empty($scopeId) ? 1 : $scopeId;
        $stockItem = $proceed($productId, $scopeId);

        $vendorId = null;
        //2018-06-01 20:53 Jing Xiao
        //ignore bundle and configurable products only, any new type of products should check
        if (!in_array($stockItem->getTypeId(), $this->notCheckTypes) && !$stockItem->hasData('qtys')) {
            $qtys = $this->helper->groupInventoryByLocationId($stockItem->getProductId(), $scopeId, $vendorId);
            if (!empty($qtys)) {
                if ($this->_state->getAreaCode() == 'adminhtml') {
                    $stockItem->setData('qty', 99999);
                    $stockItem->setData('is_in_stock', 1);
                }
                $stockItem->setData('qtys',  $qtys);
            }
        }
        if (!is_null($vendorId) && !$stockItem->hasData('vendor_id')) {
            $stockItem->setData('vendor_id', $vendorId);
        }
        $sessionVendorId = $stockItem->getSessionVendorId();
        if (!empty($sessionVendorId) && (empty($stockItem->getVendorId())
                || (!empty($this->_moVendorIds) && in_array($stockItem->getVendorId(), $this->_moVendorIds)))) {
            $stockItem->setData('vendor_id', $sessionVendorId);
        }

        return $stockItem;
    }
}