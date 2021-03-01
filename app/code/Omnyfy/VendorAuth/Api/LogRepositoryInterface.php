<?php


namespace Omnyfy\VendorAuth\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface LogRepositoryInterface
{


    /**
     * Save log
     * @param \Omnyfy\VendorAuth\Api\Data\LogInterface $log
     * @return \Omnyfy\VendorAuth\Api\Data\LogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Omnyfy\VendorAuth\Api\Data\LogInterface $log
    );

    /**
     * Retrieve log
     * @param string $logId
     * @return \Omnyfy\VendorAuth\Api\Data\LogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($logId);

    /**
     * Retrieve log matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Omnyfy\VendorAuth\Api\Data\LogSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete log
     * @param \Omnyfy\VendorAuth\Api\Data\LogInterface $log
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Omnyfy\VendorAuth\Api\Data\LogInterface $log
    );

    /**
     * Delete log by ID
     * @param string $logId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($logId);
}