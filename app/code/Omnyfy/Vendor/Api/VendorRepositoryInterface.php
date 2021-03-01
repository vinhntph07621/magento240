<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 19/6/17
 * Time: 10:34 AM
 */

namespace Omnyfy\Vendor\Api;

use Omnyfy\Vendor\Api\Data\VendorInterface;

interface VendorRepositoryInterface
{
    /**
     * @param \Omnyfy\Vendor\Api\Data\VendorInterface $vendor
     * @return bool
     */
    public function save(VendorInterface $vendor);

    /**
     * @param int $vendorId
     * @param bool $forceReload
     * @return \Omnyfy\Vendor\Api\Data\VendorInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($vendorId, $forceReload = false);

    /**
     * @param \Omnyfy\Vendor\Api\Data\VendorInterface $vendor
     * @return bool
     */
    public function delete(VendorInterface $vendor);

    /**
     * @param int $vendorId
     * @return bool
     */
    public function deleteById($vendorId);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Omnyfy\Vendor\Api\Data\VendorSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}