<?php
/**
 * Project: Favourite Vendor helper
 * Author: seth
 * Date: 9/12/19
 * Time: 12:33 pm
 **/

namespace Omnyfy\Vendor\Helper;


class FavouriteVendor extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Omnyfy\Vendor\Model\Resource\FavouriteVendor\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var Session
     */
    protected $_helperSession;


    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Omnyfy\Vendor\Model\Resource\FavouriteVendor\CollectionFactory $collectionFactory,
        \Omnyfy\Vendor\Helper\Session $helperSession
    )
    {
        parent::__construct($context);
        $this->_collectionFactory = $collectionFactory;
        $this->_helperSession = $helperSession;
    }

    /**
     * Check whether the current vendor is set as Favourite.
     *
     * @param $vendor
     * @return bool
     */
    public function isVendorSetFavourite($vendorId) {
        $customer = $this->_helperSession->getCustomer()->getId();

        $collection = $this->_collectionFactory->create();
        $collection->addFieldToFilter('vendor_id', $vendorId);
        $collection->addFieldToFilter('customer_id', $customer);

        if ($collection->getSize() > 0) {
            return true;
        }
        return false;
    }
}