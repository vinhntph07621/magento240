<?php


namespace Omnyfy\Checklist\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ChecklistItemUserUploadsRepositoryInterface
{


    /**
     * Save ChecklistItemUserUploads
     * @param \Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsInterface $checklistItemUserUploads
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsInterface $checklistItemUserUploads
    );

    /**
     * Retrieve ChecklistItemUserUploads
     * @param string $checklistitemuseruploadsId
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($checklistitemuseruploadsId);

    /**
     * Retrieve ChecklistItemUserUploads matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete ChecklistItemUserUploads
     * @param \Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsInterface $checklistItemUserUploads
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsInterface $checklistItemUserUploads
    );

    /**
     * Delete ChecklistItemUserUploads by ID
     * @param string $checklistitemuseruploadsId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($checklistitemuseruploadsId);
}
