<?php


namespace Omnyfy\VendorFeatured\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface VendorFeaturedTagRepositoryInterface
{


    /**
     * Save vendor_featured_tag
     * @param \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedTagInterface $vendorFeaturedTag
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedTagInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedTagInterface $vendorFeaturedTag
    );

    /**
     * Retrieve vendor_featured_tag
     * @param string $vendorFeaturedTagId
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedTagInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($vendorFeaturedTagId);

    /**
     * Retrieve vendor_featured_tag matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedTagSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete vendor_featured_tag
     * @param \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedTagInterface $vendorFeaturedTag
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedTagInterface $vendorFeaturedTag
    );

    /**
     * Delete vendor_featured_tag by ID
     * @param string $vendorFeaturedTagId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($vendorFeaturedTagId);
}
