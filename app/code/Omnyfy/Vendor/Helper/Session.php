<?php
/**
 * Project: Multi Vendor.
 * User: jing
 * Date: 27/8/18
 * Time: 2:51 PM
 */
namespace Omnyfy\Vendor\Helper;

class Session extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_customerSession;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        parent::__construct($context);
        $this->_customerSession = $customerSession;
    }

    public function getSessionLocationId()
    {
        return $this->_customerSession->getLocationId();
    }

    public function getSessionVendorId()
    {
        return $this->_customerSession->getVendorId();
    }

    public function getShipFromWarehouseFlag()
    {
        $flag = $this->_customerSession->getShipFromWarehouseFlag();
        return empty($flag) ? false : true;
    }

    public function getCustomer() {
        return $this->_customerSession->getCustomer();
    }

    public function isLoggedIn() {
        return $this->_customerSession->isLoggedIn();
    }
}