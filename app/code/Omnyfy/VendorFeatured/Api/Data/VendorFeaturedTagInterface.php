<?php


namespace Omnyfy\VendorFeatured\Api\Data;

interface VendorFeaturedTagInterface
{

    const VENDOR_FEATURED_ID = 'vendor_featured_id';
    const VENDOR_FEATURED_TAG_ID = 'vendor_featured_tag_id';
    const VENDOR_TAG_ID = 'vendor_tag_id';


    /**
     * Get vendor_featured_tag_id
     * @return string|null
     */
    public function getVendorFeaturedTagId();

    /**
     * Set vendor_featured_tag_id
     * @param string $vendorFeaturedTagId
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedTagInterface
     */
    public function setVendorFeaturedTagId($vendorFeaturedTagId);

    /**
     * Get vendor_featured_id
     * @return string|null
     */
    public function getVendorFeaturedId();

    /**
     * Set vendor_featured_id
     * @param string $vendorFeaturedId
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedTagInterface
     */
    public function setVendorFeaturedId($vendorFeaturedId);

    /**
     * Get vendor_tag_id
     * @return string|null
     */
    public function getVendorTagId();

    /**
     * Set vendor_tag_id
     * @param string $vendorTagId
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedTagInterface
     */
    public function setVendorTagId($vendorTagId);
}
