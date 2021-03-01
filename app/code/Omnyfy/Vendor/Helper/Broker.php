<?php
/**
 * Project: Multi Vendor.
 * User: jing
 * Date: 16/11/18
 * Time: 9:35 AM
 */
namespace Omnyfy\Vendor\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Broker extends AbstractHelper
{
    protected $_vendorResource;

    protected $_customerSession;

    protected $_vendorHelper;

    protected $_allStores;

    public function __construct(
        Context $context,
        \Omnyfy\Vendor\Model\Resource\Vendor $_vendorResource,
        \Magento\Customer\Model\Session $customerSession,
        \Omnyfy\Vendor\Helper\Data $vendorHelper,
        array $data = [])
    {
        $this->_vendorResource = $_vendorResource;
        $this->_customerSession = $customerSession;
        $this->_vendorHelper = $vendorHelper;
        parent::__construct($context);
    }

    public function getAction()
    {
        return $this->getUrl('omnyfy_vendor/store/post',
            [
                '_secure' => $this->getRequest()->isSecure()
            ]
        );
    }

    public function getCurrentStore()
    {
        $customerId = $this->_customerSession->getCustomerId();

        $vendorId = $this->_vendorResource->getFavoriteVendorIdByCustomerId($customerId);
        $stores = $this->getAllStores();
        if (!empty($vendorId) && array_key_exists($vendorId, $stores)) {
            $store = $stores[$vendorId];
            return [
                'name' => $store->getLocationName(),
                'postcode' => $store->getPostcode(),
                'vendor_id' => $vendorId
            ];
        }

        return [
            'name' => __('Not Selected'),
            'postcode' => '',
            'vendor_id' => 0
        ];
    }

    public function getAllStores()
    {
        if (null == $this->_allStores) {
            $this->_allStores = $this->_vendorHelper->getAllStores();
        }

        return $this->_allStores;
    }
}
