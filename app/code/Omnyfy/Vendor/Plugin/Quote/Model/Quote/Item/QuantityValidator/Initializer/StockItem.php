<?php
/**
 * Project: Omnyfy Multi Vendor.
 * User: jing
 * Date: 1/5/17
 * Time: 1:43 PM
 */

namespace Omnyfy\Vendor\Plugin\Quote\Model\Quote\Item\QuantityValidator\Initializer;

class StockItem
{
    protected $_stockRegistryProvider;

    protected $_stockStateProvider;

    protected $typeConfig;

    protected $quoteItemQtyList;

    protected $_extraHelper;

    protected $_moVendorIds;

    public function __construct(
        \Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface $stockRegistryProvider,
        \Magento\CatalogInventory\Model\Spi\StockStateProviderInterface $stockStateProvider,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $typeConfig,
        \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\QuoteItemQtyList $quoteItemQtyList,
        \Omnyfy\Vendor\Helper\Extra $extraHelper,
        \Omnyfy\Vendor\Model\Config $config
    )
    {
        $this->_stockRegistryProvider = $stockRegistryProvider;
        $this->_stockStateProvider = $stockStateProvider;
        $this->typeConfig = $typeConfig;
        $this->quoteItemQtyList = $quoteItemQtyList;
        $this->_extraHelper = $extraHelper;
        $this->_moVendorIds = $config->getMOVendorIds();
    }

    public function aroundInitialize(
        $subject,
        callable $proceed,
        \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem,
        \Magento\Quote\Model\Quote\Item $quoteItem,
        $qty
    )
    {
        $this->_extraHelper->parseExtraInfo(
            $quoteItem->getQuote()->getExtShippingInfo(),
            $stockItem
        );

        if ($stockItem->hasSessionLocationId() && !$stockItem->hasLocationId()) {
            $sessionLocationId = $stockItem->getSessionLocationId();
            if (!empty($sessionLocationId)) {
                $stockItem->setData('location_id', $sessionLocationId);
            }
        }

        if ($stockItem->hasSessionVendorId()) {
            $sessionVendorId = $stockItem->getSessionVendorId();
            if (!empty($sessionVendorId)) {
                if (!$stockItem->hasVendorId()
                    || (!empty($this->_moVendorIds) && in_array($stockItem->getVendorId(), $this->_moVendorIds))) {
                    $stockItem->setData('vendor_id', $sessionVendorId);
                }
            }
        }

        $result = $this->_initialize($stockItem, $quoteItem, $qty);

        if (!empty($quoteItem->getBookingId())) {
            return $result;
        }

        if ($stockItem->hasLocationId()) {
            $quoteItem->setData('location_id', $stockItem->getLocationId());
            $this->_extraHelper->updateAddressItemLocationId($quoteItem->getId(), $stockItem->getLocationId());
        }
        elseif ($stockItem->hasQtys()){
            foreach($stockItem->getQtys() as $locationId => $stockQty) {
                if (empty($locationId)) {
                    continue;
                }
                if (!$stockItem->getManageStock()) {
                    $quoteItem->setData('location_id', $locationId);
                    $this->_extraHelper->updateAddressItemLocationId($quoteItem->getId(), $stockItem->getLocationId());
                    break;
                }
                if ($stockQty - $stockItem->getMinQty() - $qty >= 0) {
                    $quoteItem->setData('location_id', $locationId);
                    $this->_extraHelper->updateAddressItemLocationId($quoteItem->getId(), $stockItem->getLocationId());
                    break;
                }
            }
        }
        if ($stockItem->hasVendorId()) {
            $quoteItem->setData('vendor_id', $stockItem->getVendorId());
        }
        return $result;
    }

    protected function _initialize(
        \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem,
        \Magento\Quote\Model\Quote\Item $quoteItem,
        $qty)
    {
        /**
         * When we work with subitem
         */
        if ($quoteItem->getParentItem()) {
            $rowQty = $quoteItem->getParentItem()->getQty() * $qty;
            /**
             * we are using 0 because original qty was processed
             */
            $qtyForCheck = $this->quoteItemQtyList
                ->getQty($quoteItem->getProduct()->getId(), $quoteItem->getId(), $quoteItem->getQuoteId(), 0);
        } else {
            $increaseQty = $quoteItem->getQtyToAdd() ? $quoteItem->getQtyToAdd() : $qty;
            $rowQty = $qty;
            $qtyForCheck = $this->quoteItemQtyList->getQty(
                $quoteItem->getProduct()->getId(),
                $quoteItem->getId(),
                $quoteItem->getQuoteId(),
                $increaseQty
            );
        }

        $productTypeCustomOption = $quoteItem->getProduct()->getCustomOption('product_type');
        if ($productTypeCustomOption !== null) {
            // Check if product related to current item is a part of product that represents product set
            if ($this->typeConfig->isProductSet($productTypeCustomOption->getValue())) {
                $stockItem->setIsChildItem(true);
            }
        }

        $stockItem->setProductName($quoteItem->getProduct()->getName());

        $toCheckStockItem = $this->_stockRegistryProvider->getStockItem(
            $quoteItem->getProduct()->getId(),
            $quoteItem->getProduct()->getStore()->getWebsiteId()
        );
        $this->_extraHelper->parseExtraInfo(
            $quoteItem->getQuote()->getExtShippingInfo(),
            $toCheckStockItem
        );
        $result = $this->_stockStateProvider->checkQuoteItemQty(
            $toCheckStockItem,
            $rowQty,
            $qtyForCheck,
            $qty
        );

        if ($stockItem->hasIsChildItem()) {
            $stockItem->unsIsChildItem();
        }

        if ($result->getItemIsQtyDecimal() !== null) {
            $quoteItem->setIsQtyDecimal($result->getItemIsQtyDecimal());
            if ($quoteItem->getParentItem()) {
                $quoteItem->getParentItem()->setIsQtyDecimal($result->getItemIsQtyDecimal());
            }
        }

        /**
         * Just base (parent) item qty can be changed
         * qty of child products are declared just during add process
         * exception for updating also managed by product type
         */
        if ($result->getHasQtyOptionUpdate() && (!$quoteItem->getParentItem() ||
                $quoteItem->getParentItem()->getProduct()->getTypeInstance()->getForceChildItemQtyChanges(
                    $quoteItem->getParentItem()->getProduct()
                )
            )
        ) {
            $quoteItem->setData('qty', $result->getOrigQty());
        }

        if ($result->getItemUseOldQty() !== null) {
            $quoteItem->setUseOldQty($result->getItemUseOldQty());
        }

        if ($result->getMessage() !== null) {
            $quoteItem->setMessage($result->getMessage());
        }

        if ($result->getItemBackorders() !== null) {
            $quoteItem->setBackorders($result->getItemBackorders());
        }

        $quoteItem->setStockStateResult($result);

        return $result;
    }
}