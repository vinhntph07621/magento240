<?php


namespace Omnyfy\VendorFeatured\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface VendorFeaturedRepositoryInterface
{


    /**
     * Save vendor_featured
     * @param \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedInterface $vendorFeatured
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedInterface $vendorFeatured
    );

    /**
     * Retrieve vendor_featured
     * @param string $vendorFeaturedId
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($vendorFeaturedId);

    /**
     * Retrieve vendor_featured matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete vendor_featured
     * @param \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedInterface $vendorFeatured
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedInterface $vendorFeatured
    );

    /**
     * Delete vendor_featured by ID
     * @param string $vendorFeaturedId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($vendorFeaturedId);
}
