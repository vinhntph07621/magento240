<?php
namespace Omnyfy\Vendor\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Shipping extends AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $_shipconfig;

    /**
     * Recipient email config path
     */
    const OMNYFY_CALCULATE_SHIPPING_BY = 'omnyfy_vendor/vendor/calculate_shipping_by';

    /**
     * Message checkout shipping enable config path
     */
    const XML_PATH_CHECKOUT_SHIPPING_MESSAGE_ENABLE = 'omnyfy_vendor/vendor/checkout_shipping_message_enable';

    /**
     * Message checkout shipping content config path
     */
    const XML_PATH_CHECKOUT_SHIPPING_MESSAGE_CONTENT = 'omnyfy_vendor/vendor/checkout_shipping_message_content';

    const XML_PATH_FREE_SHIPPING_MESSAGE_CONFIG = 'omnyfy_vendor_shipping/vendor/omnyfy_free_shipping_message_config';

    const FREE_SHIPPING_THRESHOLD = 'omnyfy_vendor_shipping/vendor/omnyfy_free_shipping_threshold';

    const ATC_MESSAGE_UNDER = 'omnyfy_vendor_shipping/vendor/atc_message_under';

    const ATC_MESSAGE_REACHED = 'omnyfy_vendor_shipping/vendor/atc_message_reached';

    const SC_MESSAGE_REACHED = 'omnyfy_vendor_shipping/vendor/sc_message_reached';

    const SC_MESSAGE_UNDER = 'omnyfy_vendor_shipping/vendor/sc_message_under';

    const SC_MESSAGE_FREE = 'omnyfy_vendor_shipping/vendor/sc_message_free';

    public function __construct(
        Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\Config $shipconfig
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_shipconfig = $shipconfig;
        parent::__construct($context);
    }


    public function getCalculateShippingBy()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return $this->_scopeConfig->getValue(self::OMNYFY_CALCULATE_SHIPPING_BY, $storeScope);
    }

    public function getShippingConfiguration($configuration)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        if (!empty($configuration)) {
            return $this->_scopeConfig->getValue('omnyfy_vendor/vendor/'.$configuration, $storeScope);
        }
    }
    
    public function getShippingConfigurationByStoreId($configuration,$storeId=null)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        if (!empty($configuration)) {
            return $this->_scopeConfig->getValue('omnyfy_vendor/vendor/'.$configuration, $storeScope, $storeId);
        }
    }

    public function getCheckoutShippingMessageEnable()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        
        return  $this->scopeConfig->getValue(self::XML_PATH_CHECKOUT_SHIPPING_MESSAGE_ENABLE, $storeScope);
    }

    public function getShippingMessageContent()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return  $this->scopeConfig->getValue(self::XML_PATH_CHECKOUT_SHIPPING_MESSAGE_CONTENT, $storeScope);
    }

    public function getFreeShippingMessageConfig()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return  $this->scopeConfig->getValue(self::XML_PATH_FREE_SHIPPING_MESSAGE_CONFIG, $storeScope);
    }

    public function getFreeShippingThreshold()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return  $this->scopeConfig->getValue(self::FREE_SHIPPING_THRESHOLD, $storeScope);
    }

    public function getAddToCartUnderMessage()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return  $this->scopeConfig->getValue(self::ATC_MESSAGE_UNDER, $storeScope);
    }

    public function getAddToCartReachedMessage()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return  $this->scopeConfig->getValue(self::ATC_MESSAGE_REACHED, $storeScope);
    }

    public function getShoppingCartMessageUnder()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return  $this->scopeConfig->getValue(self::SC_MESSAGE_UNDER, $storeScope);
    }

    public function getShoppingCartMessageReached()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return  $this->scopeConfig->getValue(self::SC_MESSAGE_REACHED, $storeScope);
    }

    public function getShoppingCartMessageFreeShipping()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return  $this->scopeConfig->getValue(self::SC_MESSAGE_FREE, $storeScope);
    }

    public function getShippingMethods(){
        $activeCarriers = $this->_shipconfig->getActiveCarriers();
        $options = array();
            foreach($activeCarriers as $carrierCode => $carrierModel)
            {
                if( $carrierMethods = $carrierModel->getAllowedMethods() )
                {
                    foreach ($carrierMethods as $methodCode => $method)
                    {
                            $code= $carrierCode.'_'.$methodCode;
                            $options[]=$code;

                    }
                }
            }

        return $options;        
    }
}
