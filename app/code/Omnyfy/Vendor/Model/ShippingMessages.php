<?php

namespace Omnyfy\Vendor\Model;

use Omnyfy\Vendor\Api\ShippingMessagesRepositoryInterface;
use Psr\Log\NullLogger;

class ShippingMessages implements ShippingMessagesRepositoryInterface
{
    protected $_shippingHelper;

    protected $vendorFactory;

    public function __construct(
        \Omnyfy\Vendor\Helper\Shipping $shippingHelper,
        \Omnyfy\Vendor\Model\VendorFactory $vendorFactory
    )
    {
        $this->_shippingHelper = $shippingHelper;
        $this->vendorFactory = $vendorFactory;
    }


    /**
     * @api
     * @param string $type
     * @return \Omnyfy\Core\Api\Json
     */
    public function getShippingConfig($type)
    {
        switch ($type) {
            case "freeshippingthreshold":
                $message = $this->_shippingHelper->getFreeShippingThreshold();
                break;
            case "shoppingcartmessagefree":
                $message = $this->_shippingHelper->getShoppingCartMessageFreeShipping();
                break;
            case "addtocartunder":
                $message = $this->_shippingHelper->getAddToCartUnderMessage();
                break;
            case "addtocartreached":
                $message = $this->_shippingHelper->getAddToCartReachedMessage();
                break;
            case "shoppingcartunder":
                $message = $this->_shippingHelper->getShoppingCartMessageUnder();
                break;
            case "shoppingcartreached":
                $message = $this->_shippingHelper->getShoppingCartMessageReached();
                break;
            case "shippingmessagecontent":
                $message = $this->_shippingHelper->getShippingMessageContent();
                break;
            default:
                $message = 'Not a valid request';
        }

        $messageResponse = [$message];

        return $messageResponse;
    }

    /**
     * @api
     * @param int $id
     * @return \Omnyfy\Core\Api\Json
     */
    public function getVendorThreshold($id)
    {
        $vendor = $this->vendorFactory->create()->load($id);

        $vendorData = [];

        if (!empty($vendor->getVfreeShippingThreshold())) {
            $vendorData['free_shipping_threshold'] = $vendor->getVfreeShippingThreshold();
        }


        return $vendorData;
    }
}