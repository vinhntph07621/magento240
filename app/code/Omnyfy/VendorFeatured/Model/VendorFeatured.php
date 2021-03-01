<?php


namespace Omnyfy\VendorFeatured\Model;

use Omnyfy\VendorFeatured\Api\Data\VendorFeaturedInterface;

class VendorFeatured extends \Magento\Framework\Model\AbstractModel implements VendorFeaturedInterface
{

    protected $_eventPrefix = 'omnyfy_vendorfeatured_vendor_featured';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeatured');
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
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedInterface
     */
    public function setVendorFeaturedId($vendorFeaturedId)
    {
        return $this->setData(self::VENDOR_FEATURED_ID, $vendorFeaturedId);
    }

    /**
     * Get vendor_id
     * @return string
     */
    public function getVendorId()
    {
        return $this->getData(self::VENDOR_ID);
    }

    /**
     * Set vendor_id
     * @param string $vendorId
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedInterface
     */
    public function setVendorId($vendorId)
    {
        return $this->setData(self::VENDOR_ID, $vendorId);
    }

    /**
     * Get added_date
     * @return string
     */
    public function getAddedDate()
    {
        return $this->getData(self::ADDED_DATE);
    }

    /**
     * Set added_date
     * @param string $addedDate
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedInterface
     */
    public function setAddedDate($addedDate)
    {
        return $this->setData(self::ADDED_DATE, $addedDate);
    }

    /**
     * Get updated_date
     * @return string
     */
    public function getUpdatedDate()
    {
        return $this->getData(self::UPDATED_DATE);
    }

    /**
     * Set updated_date
     * @param string $updatedDate
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedInterface
     */
    public function setUpdatedDate($updatedDate)
    {
        return $this->setData(self::UPDATED_DATE, $updatedDate);
    }

    public function getVendorTags(){
        return $this->getData('vendor_tags');
    }
}
