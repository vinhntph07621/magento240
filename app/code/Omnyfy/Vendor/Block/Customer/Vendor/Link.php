<?php
/**
 * Project: Multi Vendor.
 * User: jing
 * Date: 12/11/18
 * Time: 4:54 PM
 */
namespace Omnyfy\Vendor\Block\Customer\Vendor;


class Link extends \Magento\Framework\View\Element\Template
{
    protected $_customerSession;

    protected $_vendorResource;

    protected $_vendorHelper;

    protected $_allStores;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\CustomerData\JsLayoutDataProviderPoolInterface $jsLayoutDataProvider,
        \Omnyfy\Vendor\Model\Resource\Vendor $_vendorResource,
        \Omnyfy\Vendor\Helper\Data $vendorHelper,
        array $data = [])
    {
        $this->_customerSession = $customerSession;
        $this->_vendorResource = $_vendorResource;
        $this->_vendorHelper = $vendorHelper;
        if (isset($data['jsLayout'])) {
            $this->jsLayout = array_merge_recursive($jsLayoutDataProvider->getData(), $data['jsLayout']);
            unset($data['jsLayout']);
        } else {
            $this->jsLayout = $jsLayoutDataProvider->getData();
        }
        parent::__construct($context, $data);
    }

    public function isLoggedIn()
    {
        return $this->_customerSession->isLoggedIn();
    }

    public function getCurrentStore()
    {
        $customerId = $this->_customerSession->getCustomerId();

        $vendorId = $this->_vendorResource->getFavoriteVendorIdByCustomerId($customerId);
        $stores = $this->getAllStores();
        if (!empty($vendorId) && array_key_exists($vendorId, $stores)) {
            $store = $stores[$vendorId];
            return [
                'name' => $store->getVendorName(),
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

    public function getVendorUrl($vendorId)
    {
        return $this->getUrl('omnyfy_vendor/brands/view', ['id' => $vendorId]);
    }

    public function getAllStores()
    {
        if (null == $this->_allStores) {
            $this->_allStores = $this->_vendorHelper->getAllStores();
        }

        return $this->_allStores;
    }
}
