<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 15/8/17
 * Time: 10:09 AM
 */
namespace Omnyfy\Vendor\Block\Vendor;

use Magento\Framework\View\Element\Template;

class Listing extends \Magento\Framework\View\Element\Template
{
    protected $_vendorCollection;

    protected $vendorFactory;

    protected $helper;

    public function __construct(
        Template\Context $context,
        \Omnyfy\Vendor\Model\VendorFactory $vendorFactory,
        \Omnyfy\Vendor\Helper\Media $helper,
        array $data = [])
    {
        $this->vendorFactory = $vendorFactory;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        $this->setMode('grid');
        parent::_construct();
    }

    public function getLoadedVendorCollection()
    {
        if ($this->_vendorCollection === null) {
            $collection = $this->vendorFactory->create()->getCollection();
            $websiteId = $this->_storeManager->getWebsite()->getId();
            $collection->filterWebsite($websiteId);
            $collection->addFieldToFilter('status', 1);

            if ($collection->isEnabledFlat()) {
                $collection->addFieldToSelect('*');
            }
            else {
                $collection->addFieldToSelect('logo');
            }

            $this->_vendorCollection = $collection;
        }
        return $this->_vendorCollection;
    }

    public function getVendorUrl($vendor)
    {
        return $this->getUrl('*/*/view', ['id' => $vendor->getId()]);
    }

    public function getLogoUrl($vendor)
    {
        $logo = $vendor->getLogo();
        if (empty($logo)) {
            return false;
        }
        return $this->helper->getVendorLogoUrl($vendor);
    }
}