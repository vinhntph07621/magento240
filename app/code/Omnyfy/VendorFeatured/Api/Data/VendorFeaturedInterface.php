<?php


namespace Omnyfy\VendorFeatured\Api\Data;

interface VendorFeaturedInterface
{

    const UPDATED_DATE = 'updated_date';
    const VENDOR_FEATURED_ID = 'vendor_featured_id';
    const ADDED_DATE = 'added_date';
    const VENDOR_ID = 'vendor_id';


    /**
     * Get vendor_featured_id
     * @return string|null
     */
    public function getVendorFeaturedId();

    /**
     * Set vendor_featured_id
     * @param string $vendorFeaturedId
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedInterface
     */
    public function setVendorFeaturedId($vendorFeaturedId);

    /**
     * Get vendor_id
     * @return string|null
     */
    public function getVendorId();

    /**
     * Set vendor_id
     * @param string $vendorId
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedInterface
     */
    public function setVendorId($vendorId);

    /**
     * Get added_date
     * @return string|null
     */
    public function getAddedDate();

    /**
     * Set added_date
     * @param string $addedDate
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedInterface
     */
    public function setAddedDate($addedDate);

    /**
     * Get updated_date
     * @return string|null
     */
    public function getUpdatedDate();

    /**
     * Set updated_date
     * @param string $updatedDate
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedInterface
     */
    public function setUpdatedDate($updatedDate);

    /**
     * Get updated_date
     * @return string|null
     */
    public function getVendorTags();
}
