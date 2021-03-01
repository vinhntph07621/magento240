<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-03
 * Time: 13:46
 */
namespace Omnyfy\Vendor\Api;

use Omnyfy\Vendor\Api\Data\VendorTypeInterface;

interface VendorTypeRepositoryInterface
{
    /**
     * @param \Omnyfy\Vendor\Api\Data\VendorTypeInterface $vendorType
     * @return bool
     */
    public function save(VendorTypeInterface $vendorType);

    /**
     * @param int $vendorTypeId
     * @param bool $forceReload
     * @return \Omnyfy\Vendor\Api\Data\VendorTypeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($vendorTypeId, $forceReload = false);

    /**
     * @param \Omnyfy\Vendor\Api\Data\VendorTypeInterface $vendorType
     * @return bool
     */
    public function delete(VendorTypeInterface $vendorType);

    /**
     * @param int $vendorTypeId
     * @return bool
     */
    public function deleteById($vendorTypeId);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Omnyfy\Vendor\Api\Data\VendorTypeSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
 