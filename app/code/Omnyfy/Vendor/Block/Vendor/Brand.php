<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 15/8/17
 * Time: 3:54 PM
 */
namespace Omnyfy\Vendor\Block\Vendor;

use Magento\Framework\View\Element\Template;

class Brand extends \Magento\Framework\View\Element\Template
{
    protected $coreRegistry;

    protected $vendorFactory;

    protected $_helper;

    protected $_template = "widget/brand.phtml";

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Omnyfy\Vendor\Model\VendorFactory $vendorFactory,
        \Omnyfy\Vendor\Helper\Media $helper,
        array $data = [])
    {
        $this->coreRegistry = $coreRegistry;
        $this->vendorFactory = $vendorFactory;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    public function getVendor(){
        $product = $this->coreRegistry->registry('product');
        if (empty($product) || $product->getId() ==0) {
            return false;
        }

        if ($this->_scopeConfig->isSetFlag(\Omnyfy\Vendor\Model\Config::XML_PATH_VENDOR_SHARE_PRODS)) {
            return false;
        }

        $vendor = $this->vendorFactory->create();

        $vendorId = $vendor->getResource()->getVendorIdByProductId($product->getId());
        if (empty($vendorId)) {
            return false;
        }
        $vendor->load($vendorId);
        if ($vendor->getId() == $vendorId) {
            return $vendor;
        }
        return false;
    }

    public function getLogoUrl($vendor)
    {
        return $this->_helper->getVendorLogoUrl($vendor);
    }

    public function getVendorUrl($vendor){
        return $this->getUrl('omnyfy_vendor/brands/view', ['id' => $vendor->getId()]);
    }
}