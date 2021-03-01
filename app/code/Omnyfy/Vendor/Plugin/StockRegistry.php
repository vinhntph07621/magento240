<?php
/**
 * Project: Omnyfy Multi Vendor.
 * User: jing
 * Date: 26/4/17
 * Time: 5:19 PM
 */
namespace Omnyfy\Vendor\Plugin;

class StockRegistry
{
    protected $stockRegistryProvider;

    protected $stockConfiguration;

    public function __construct(
        \Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface $stockRegistryProvider,
        \Magento\CatalogInventory\Model\Configuration $stockConfiguration
    )
    {
        $this->stockRegistryProvider = $stockRegistryProvider;
        $this->stockConfiguration = $stockConfiguration;
    }

    public function aroundGetStockItem(
        $subject,
        callable $proceed,
        $productId,
        $scopeId = null)
    {
        if (!$scopeId) {
            return $proceed($productId);
        }

        if (!$scopeId) {
            $scopeId = $this->stockConfiguration->getDefaultScopeId();
        }

        return $this->stockRegistryProvider->getStockItem($productId, $scopeId);
    }
}