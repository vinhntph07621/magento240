<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-06-11
 * Time: 16:15
 */
namespace Omnyfy\Vendor\Api;

interface SearchRepositoryInterface
{
    /**
     * Get search result
     * @param string $vendorTypeId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Omnyfy\Vendor\Api\Data\VendorSearchResultsInterface
     */
    public function getList($vendorTypeId, $searchCriteria);
}
 