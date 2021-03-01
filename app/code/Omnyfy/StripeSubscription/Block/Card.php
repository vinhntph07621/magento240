<?php
/**
 * Project: Stripe Subscription
 * User: jing
 * Date: 2019-08-02
 * Time: 15:23
 */
namespace Omnyfy\StripeSubscription\Block;

class Card extends \Magento\Framework\View\Element\Template
{
    protected $_registry;

    protected $_helper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Omnyfy\StripeApi\Helper\Data $helper,
        array $data = []
    ) {
        $this->_registry = $coreRegistry;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    public function getVendorType() {
        return $this->_registry->registry('current_omnyfy_vendor_type');
    }

    public function getStripeApiKey() {
        return $this->_helper->getPublicKey();
    }

    public function getVendorPlan() {
        return $this->_registry->registry('current_omnyfy_plan');
    }
}
 