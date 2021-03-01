<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 23/9/19
 * Time: 4:36 pm
 */
namespace Omnyfy\Vendor\Observer;

class CustomerLogin implements \Magento\Framework\Event\ObserverInterface
{
    protected $moduleManager;

    protected $checkoutSession;

    protected $_bindRepository;

    protected $_helper;

    protected $_vendorResource;

    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Omnyfy\Vendor\Model\BindRepository $bindRepository,
        \Omnyfy\Vendor\Model\Resource\Vendor $_vendorResource,
        \Omnyfy\Vendor\Helper\Extra $_helper
    )
    {
        $this->moduleManager = $moduleManager;
        $this->checkoutSession = $checkoutSession;
        $this->_bindRepository = $bindRepository;
        $this->_vendorResource = $_vendorResource;
        $this->_helper = $_helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->moduleManager->isEnabled('Omnyfy_Multishipping')) {
            return;
        }

        $quoteId = $this->checkoutSession->getQuoteId();
        if (empty($quoteId)) {
            return;
        }

        $customer = $observer->getCustomer();
        if (empty($customer) || empty($customer->getId())) {
            return;
        }
        $customerId = $customer->getId();

        $favoriteVendorId = $this->_vendorResource->getFavoriteVendorIdByCustomerId($customerId);
        if ($favoriteVendorId) {
            $extraInfo = $this->checkoutSession->getQuote()->getExtShippingInfo();
            $extraInfo = empty($extraInfo) ? [] : json_decode($extraInfo, true);

            $extraInfo['vendor_id'] = $favoriteVendorId;
            $this->_helper->updateExtraInfo($quoteId, $extraInfo);
        }
    }
}
 