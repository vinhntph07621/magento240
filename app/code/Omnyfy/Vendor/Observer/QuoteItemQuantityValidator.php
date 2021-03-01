<?php
/**
 * Project: Multi Vendors.
 * User: jing
 * Date: 18/2/18
 * Time: 1:36 PM
 */
namespace Omnyfy\Vendor\Observer;

class QuoteItemQuantityValidator implements \Magento\Framework\Event\ObserverInterface
{
    protected $stockRegistry;

    protected $optionInitializer;

    protected $_extraHelper;

    protected $_includeProductType = [
        \Magento\Catalog\Model\Product\Type::DEFAULT_TYPE,
        \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
        \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE,
        \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE,
        \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE
    ];

    public function __construct(
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\Option $optionInitializer,
        \Omnyfy\Vendor\Helper\Extra $extraHelper
    )
    {
        $this->stockRegistry = $stockRegistry;
        $this->optionInitializer = $optionInitializer;
        $this->_extraHelper = $extraHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quoteItem = $observer->getEvent()->getItem();
        //location_id and vendor_id missing in super mode.
        //Try to add location_id and vendor
        if (!$quoteItem ||
            !$quoteItem->getProductId() ||
            !$quoteItem->getQuote() ||
            !$quoteItem->getQuote()->getIsSuperMode()
        ) {
            return;
        }

        //Do nothing if location_id and vendor_id already set
        if ($quoteItem->hasLocationId() && $quoteItem->hasVendorId()) {
            return;
        }

        //leave new product types check in their module
        if ($quoteItem->hasBookingId()
            ||  !in_array($quoteItem->getProductType(), $this->_includeProductType)) {
            return;
        }

        $product = $quoteItem->getProduct();
        $qty = $quoteItem->getQty();

        /** @var \Magento\CatalogInventory\Model\Stock\Item $stockItem */
        $stockItem = $this->stockRegistry->getStockItem(
            $product->getId(),
            $product->getStore()->getWebsiteId()
        );

        if ($stockItem->hasLocationId()) {
            $quoteItem->setData('location_id', $stockItem->getLocationId());
            $this->_extraHelper->updateAddressItemLocationId($quoteItem->getId(), $stockItem->getLocationId());
        }
        elseif ($stockItem->hasQtys()){
            $defaultLocationId = 0;
            foreach($stockItem->getQtys() as $locationId => $stockQty) {
                if (empty($defaultLocationId)) {
                    $defaultLocationId = $locationId;
                }
                if ($stockQty - $qty >= 0 && $stockQty - $stockItem->getMinQty() >= 0) {
                    $quoteItem->setData('location_id', $locationId);
                    $this->_extraHelper->updateAddressItemLocationId($quoteItem->getId(), $locationId);
                    break;
                }
            }
            if (!$quoteItem->hasLocationId()) {
                $quoteItem->setData('location_id', $defaultLocationId);
                $this->_extraHelper->updateAddressItemLocationId($quoteItem->getId(), $defaultLocationId);
            }
        }

        if ($stockItem->hasVendorId()) {
            $quoteItem->setData('vendor_id', $stockItem->getVendorId());
        }

        if (($options = $quoteItem->getQtyOptions()) && $qty > 0) {
            $qty = $product->getTypeInstance()->prepareQuoteItemQty($qty, $product);
            $quoteItem->setData('qty', $qty);

            foreach ($options as $option) {
                $result = $this->optionInitializer->initialize($option, $quoteItem, $qty);
                if ($result->getHasError()) {
                    $option->setHasError(true);

                    $quoteItem->addErrorInfo(
                        'cataloginventory',
                        \Magento\CatalogInventory\Helper\Data::ERROR_QTY,
                        $result->getMessage()
                    );

                    $quoteItem->getQuote()->addErrorInfo(
                        $result->getQuoteMessageIndex(),
                        'cataloginventory',
                        \Magento\CatalogInventory\Helper\Data::ERROR_QTY,
                        $result->getQuoteMessage()
                    );
                } else {
                    // Delete error from item and its quote, if it was set due to qty lack
                    $this->_removeErrorsFromQuoteAndItem(
                        $quoteItem,
                        \Magento\CatalogInventory\Helper\Data::ERROR_QTY
                    );
                }
            }
        }
    }

    protected function _removeErrorsFromQuoteAndItem($item, $code)
    {
        if ($item->getHasError()) {
            $params = ['origin' => 'cataloginventory', 'code' => $code];
            $item->removeErrorInfosByParams($params);
        }

        $quote = $item->getQuote();
        $quoteItems = $quote->getItemsCollection();
        $canRemoveErrorFromQuote = true;

        foreach ($quoteItems as $quoteItem) {
            if ($quoteItem->getItemId() == $item->getItemId()) {
                continue;
            }

            $errorInfos = $quoteItem->getErrorInfos();
            foreach ($errorInfos as $errorInfo) {
                if ($errorInfo['code'] == $code) {
                    $canRemoveErrorFromQuote = false;
                    break;
                }
            }

            if (!$canRemoveErrorFromQuote) {
                break;
            }
        }

        if ($quote->getHasError() && $canRemoveErrorFromQuote) {
            $params = ['origin' => 'cataloginventory', 'code' => $code];
            $quote->removeErrorInfosByParams(null, $params);
        }
    }
}