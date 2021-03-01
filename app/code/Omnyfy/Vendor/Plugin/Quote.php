<?php
/**
 * Project: Vendor
 * User: jing
 * Date: 23/9/19
 * Time: 6:01 pm
 */
namespace Omnyfy\Vendor\Plugin;

class Quote
{
    protected $moduleManager;

    protected $_vendorResource;

    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Omnyfy\Vendor\Model\Resource\Vendor $_vendorResource
    ) {
        $this->moduleManager = $moduleManager;
        $this->_vendorResource = $_vendorResource;
    }

    public function afterBeforeSave($subject, $result)
    {
        if ($this->moduleManager->isEnabled('Omnyfy_Multishipping')) {
            return $result;
        }

        if ($customerId = $subject->getCustomerId()) {
            $extraInfo = $subject->getExtShippingInfo();
            $extraInfo = empty($extraInfo) ? [] : json_decode($extraInfo, true);

            $favoriteVendorId = $this->_vendorResource->getFavoriteVendorIdByCustomerId($customerId);
            if ($favoriteVendorId) {
                $extraInfo['vendor_id'] = $favoriteVendorId;
                $subject->setExtShippingInfo(json_encode($extraInfo));
            }
        }
        return $result;
    }
}
 