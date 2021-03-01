<?php


namespace Omnyfy\VendorFeatured\Model;

use Omnyfy\VendorFeatured\Api\Data\VendorTagInterface;

class VendorTag extends \Magento\Framework\Model\AbstractModel implements VendorTagInterface
{

    protected $_eventPrefix = 'omnyfy_vendorfeatured_vendor_tag';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\VendorFeatured\Model\ResourceModel\VendorTag');
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
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorTagInterface
     */
    public function setVendorTagId($vendorTagId)
    {
        return $this->setData(self::VENDOR_TAG_ID, $vendorTagId);
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set name
     * @param string $name
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorTagInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }
}
