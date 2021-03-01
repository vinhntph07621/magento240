<?php
/**
 * Project: Omnyfy Multi Vendor.
 * User: jing
 * Date: 10/5/17
 * Time: 11:43 AM
 */
namespace Omnyfy\Vendor\Plugin\Quote\Model\Cart;

class ShippingMethodConverter
{
    protected $extensionFactory;

    public function __construct(
        \Magento\Quote\Api\Data\ShippingMethodExtensionFactory $extensionFactory
    )
    {
        $this->extensionFactory = $extensionFactory;
    }

    public function aroundModelToDataObject(
        \Magento\Quote\Model\Cart\ShippingMethodConverter $subject,
        callable $proceed,
        $rateModel,
        $quoteCurrencyCode
        )
    {
        $result = $proceed($rateModel, $quoteCurrencyCode);
        $extensionAttributes = $result->getExtensionAttributes();
        $extensionAttributes = empty($extensionAttributes) ? $this->extensionFactory->create() : $extensionAttributes;
        if ($rateModel->hasData('location_id')) {
            $extensionAttributes->setLocationId($rateModel->getLocationId());
        }
        if ($rateModel->hasData('vendor_id')) {
            $extensionAttributes->setVendorId($rateModel->getVendorId());
        }
        $result->setExtensionAttributes($extensionAttributes);

        return $result;
    }
}