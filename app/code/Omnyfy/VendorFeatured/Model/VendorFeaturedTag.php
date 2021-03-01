<?php


namespace Omnyfy\VendorFeatured\Model;

use Omnyfy\VendorFeatured\Api\Data\VendorFeaturedTagInterface;

class VendorFeaturedTag extends \Magento\Framework\Model\AbstractModel implements VendorFeaturedTagInterface
{

    protected $_eventPrefix = 'omnyfy_vendorfeatured_vendor_featured_tag';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeaturedTag');
    }

    /**
     * Get vendor_featured_tag_id
     * @return string
     */
    public function getVendorFeaturedTagId()
    {
        return $this->getData(self::VENDOR_FEATURED_TAG_ID);
    }

    /**
     * Set vendor_featured_tag_id
     * @param string $vendorFeaturedTagId
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedTagInterface
     */
    public function setVendorFeaturedTagId($vendorFeaturedTagId)
    {
        return $this->setData(self::VENDOR_FEATURED_TAG_ID, $vendorFeaturedTagId);
    }

    /**
     * Get vendor_featured_id
     * @return string
     */
    public function getVendorFeaturedId()
    {
        return $this->getData(self::VENDOR_FEATURED_ID);
    }

    /**
     * Set vendor_featured_id
     * @param string $vendorFeaturedId
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedTagInterface
     */
    public function setVendorFeaturedId($vendorFeaturedId)
    {
        return $this->setData(self::VENDOR_FEATURED_ID, $vendorFeaturedId);
    }

    /**
     * Get vendor_tag_id
     * @return string
     */
    public function getVendorTagId()
    {
        return $this->getData(self::VENDOR_TAG_ID);
    }

    /**
     * Set vendor_tag_id
     * @param string $vendorTagId
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedTagInterface
     */
    public function setVendorTagId($vendorTagId)
    {
        return $this->setData(self::VENDOR_TAG_ID, $vendorTagId);
    }
}
