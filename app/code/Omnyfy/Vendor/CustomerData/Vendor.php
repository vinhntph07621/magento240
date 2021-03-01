<?php
/**
 * Project: Multi Vendor.
 * User: jing
 * Date: 12/11/18
 * Time: 6:03 PM
 */
namespace Omnyfy\Vendor\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;

class Vendor extends \Magento\Framework\DataObject implements SectionSourceInterface
{
    protected $_customerSession;

    protected $_locationCollectionFactory;

    protected $_vendorResource;

    protected $_vendorHelper;

    protected $summeryCount;

    protected $_allStores;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Omnyfy\Vendor\Model\Resource\Location\CollectionFactory $locationCollectionFactory,
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource,
        \Omnyfy\Vendor\Helper\Data $vendorHelper,
        array $data = [])
    {
        $this->_customerSession = $customerSession;
        $this->_locationCollectionFactory = $locationCollectionFactory;
        $this->_vendorResource = $vendorResource;
        $this->_vendorHelper = $vendorHelper;
        parent::__construct($data);
    }

    public function getSectionData()
    {
        return $this->getCustomerStore();
    }

    public function getCustomerStore()
    {
        $customerId = $this->_customerSession->getCustomerId();
        if (empty($customerId)) {
            return [];
        }

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
            'name' => 'To Select',
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