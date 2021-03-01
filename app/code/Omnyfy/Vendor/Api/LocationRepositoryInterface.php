<?php

namespace Omnyfy\Vendor\Api;

use Omnyfy\Vendor\Api\Data\LocationInterface;

interface LocationRepositoryInterface
{

    /**
     * Save
     *
     * @param \Omnyfy\Vendor\Api\Data\LocationInterface $location
     * @return bool
     */
    public function save(LocationInterface $location);

    /**
     * Get by id
     *
     * @param int $locationId
     * @param bool $forceReload
     * @return \Omnyfy\Vendor\Api\Data\LocationInterface
     */
    public function getById($locationId, $forceReload = false);

    /**
     * Delete
     *
     * @param \Omnyfy\Vendor\Api\Data\LocationInterface $location
     * @return bool Will returned True if deleted
     */
    public function delete(LocationInterface $location);

    /**
     * Delete by id
     *
     * @param int $locaitonId
     * @return bool Will returned True if deleted
     */
    public function deleteById($locationId);

    /**
     * Get list
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Omnyfy\Vendor\Api\Data\LocationSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Get list vendor warehouse mapping
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Omnyfy\Vendor\Api\Data\LocationSearchResultsInterface
     */
    public function getListVendorWarehouse(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Get list by keyword
     *
     * @param string $keyword
     * @return \Omnyfy\Vendor\Api\Data\LocationSimpleParameterSearchInterface
     */
    public function getListByKeyword($keyword);

}
