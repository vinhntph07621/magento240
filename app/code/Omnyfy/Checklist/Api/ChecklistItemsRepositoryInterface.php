<?php


namespace Omnyfy\Checklist\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ChecklistItemsRepositoryInterface
{


    /**
     * Save ChecklistItems
     * @param \Omnyfy\Checklist\Api\Data\ChecklistItemsInterface $checklistItems
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Omnyfy\Checklist\Api\Data\ChecklistItemsInterface $checklistItems
    );

    /**
     * Retrieve ChecklistItems
     * @param string $checklistitemsId
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($checklistitemsId);

    /**
     * Retrieve ChecklistItems matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemsSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete ChecklistItems
     * @param \Omnyfy\Checklist\Api\Data\ChecklistItemsInterface $checklistItems
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Omnyfy\Checklist\Api\Data\ChecklistItemsInterface $checklistItems
    );

    /**
     * Delete ChecklistItems by ID
     * @param string $checklistitemsId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($checklistitemsId);
}
