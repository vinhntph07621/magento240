<?php
/**
 * Project: Omnyfy Multi Vendor.
 * User: jing
 * Date: 1/5/17
 * Time: 1:43 PM
 */
namespace Omnyfy\Vendor\Plugin\Quote\Model\Quote\Item\QuantityValidator\Initializer;

class Option
{
    protected $_extraHelper;

    protected $_moVendorIds;

    public function __construct(
        \Omnyfy\Vendor\Helper\Extra $extraHelper,
        \Omnyfy\Vendor\Model\Config $config
    ) {
        $this->_extraHelper = $extraHelper;
        $this->_moVendorIds = $config->getMOVendorIds();
    }

    public function aroundInitialize(
        $subject,
        callable $proceed,
        \Magento\Quote\Model\Quote\Item\Option $option,
        \Magento\Quote\Model\Quote\Item $quoteItem,
        $qty)
    {
         $result = $proceed($option, $quoteItem, $qty);
         $stockItem = $subject->getStockItem($option, $quoteItem);
         if ($stockItem->hasLocationId()) {
             $quoteItem->setData('location_id', $stockItem->getLocationId());
             $this->_extraHelper->updateAddressItemLocationId($quoteItem->getId(), $stockItem->getLocationId());
         }
         elseif ($stockItem->hasQtys()) {
             foreach($stockItem->getQtys() as $locationId => $stockQty) {
                 if (!$stockItem->getManageStock()) {
                     $stockItem->setData('location_id', $locationId);
                     $quoteItem->setData('location_id', $locationId);
                     $this->_extraHelper->updateAddressItemLocationId($quoteItem->getId(), $locationId);
                     break;
                 }
                 if ($stockQty - $stockItem->getMinQty() - $qty >= 0) {
                     $stockItem->setData('location_id', $locationId);
                     $quoteItem->setData('location_id', $locationId);
                     $this->_extraHelper->updateAddressItemLocationId($quoteItem->getId(), $locationId);
                     break;
                 }
             }
         }
         if ($stockItem->hasVendorId()) {
             $quoteItem->setData('vendor_id', $stockItem->getVendorId());
         }
         $sessionVendorId = $this->_extraHelper->getSessionVendorId($quoteItem->getQuote());
         if (!empty($sessionVendorId) && (!empty($this->_moVendorIds) && in_array($quoteItem->getVendorId(), $this->_moVendorIds))) {
             $quoteItem->setData('vendor_id', $sessionVendorId);
             $stockItem->setData('session_vendor_id', $sessionVendorId);
             $stockItem->setData('vendor_id', $sessionVendorId);
         }
         return $result;
    }
}