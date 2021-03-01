<?php
/**
 * Project: apcd.
 * User: jing
 * Date: 19/11/18
 * Time: 12:06 AM
 */
namespace Omnyfy\Vendor\Block\Customer\Vendor;

use Magento\Framework\View\Element\Template;

class Form extends \Magento\Framework\View\Element\Template
{
    protected $_vendorResource;

    protected $_customerSession;

    protected $_vendorHelper;

    protected $_allStores;

    public function __construct(
        Template\Context $context,
        \Omnyfy\Vendor\Model\Resource\Vendor $_vendorResource,
        \Magento\Customer\Model\Session $customerSession,
        \Omnyfy\Vendor\Helper\Data $vendorHelper,
        array $data = [])
    {
        $this->_vendorResource = $_vendorResource;
        $this->_customerSession = $customerSession;
        $this->_vendorHelper = $vendorHelper;
        parent::__construct($context, $data);
    }

    public function getActionUrl()
    {
        return $this->getUrl('omnyfy_vendor/store/edit');
    }


    public function getCurrentStore()
    {
        $customerId = $this->_customerSession->getCustomerId();

        $vendorId = $this->_vendorResource->getFavoriteVendorIdByCustomerId($customerId);
        $stores = $this->getAllStores();
        if (!empty($vendorId) && array_key_exists($vendorId, $stores)) {
            $store = $stores[$vendorId];
            return $vendorId;
        }

        return 0;
    }

    public function isLoggedIn()
    {
        if ($this->_customerSession->isLoggedIn())
            return 1;

        return 0;
    }

    public function getAllStores()
    {
        if (null == $this->_allStores) {
            $this->_allStores = $this->_vendorHelper->getAllStores();
        }

        return $this->_allStores;
    }
}