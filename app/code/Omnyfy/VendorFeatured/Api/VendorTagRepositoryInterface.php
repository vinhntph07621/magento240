<?php


namespace Omnyfy\VendorFeatured\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface VendorTagRepositoryInterface
{


    /**
     * Save vendor_tag
     * @param \Omnyfy\VendorFeatured\Api\Data\VendorTagInterface $vendorTag
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorTagInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Omnyfy\VendorFeatured\Api\Data\VendorTagInterface $vendorTag
    );

    /**
     * Retrieve vendor_tag
     * @param string $vendorTagId
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorTagInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($vendorTagId);

    /**
     * Retrieve vendor_tag matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorTagSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete vendor_tag
     * @param \Omnyfy\VendorFeatured\Api\Data\VendorTagInterface $vendorTag
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Omnyfy\VendorFeatured\Api\Data\VendorTagInterface $vendorTag
    );

    /**
     * Delete vendor_tag by ID
     * @param string $vendorTagId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($vendorTagId);
}
