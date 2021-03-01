<?php


namespace Omnyfy\VendorFeatured\Api\Data;

interface VendorTagInterface
{

    const NAME = 'name';
    const VENDOR_TAG_ID = 'vendor_tag_id';


    /**
     * Get vendor_tag_id
     * @return string|null
     */
    public function getVendorTagId();

    /**
     * Set vendor_tag_id
     * @param string $vendorTagId
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorTagInterface
     */
    public function setVendorTagId($vendorTagId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorTagInterface
     */
    public function setName($name);
}
