<?php


namespace Omnyfy\Checklist\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ChecklistItemUploadsRepositoryInterface
{


    /**
     * Save ChecklistItemUploads
     * @param \Omnyfy\Checklist\Api\Data\ChecklistItemUploadsInterface $checklistItemUploads
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUploadsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Omnyfy\Checklist\Api\Data\ChecklistItemUploadsInterface $checklistItemUploads
    );

    /**
     * Retrieve ChecklistItemUploads
     * @param string $checklistitemuploadsId
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUploadsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($checklistitemuploadsId);

    /**
     * Retrieve ChecklistItemUploads matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUploadsSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete ChecklistItemUploads
     * @param \Omnyfy\Checklist\Api\Data\ChecklistItemUploadsInterface $checklistItemUploads
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Omnyfy\Checklist\Api\Data\ChecklistItemUploadsInterface $checklistItemUploads
    );

    /**
     * Delete ChecklistItemUploads by ID
     * @param string $checklistitemuploadsId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($checklistitemuploadsId);
}
