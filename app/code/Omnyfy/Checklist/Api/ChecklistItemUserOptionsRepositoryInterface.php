<?php


namespace Omnyfy\Checklist\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ChecklistItemUserOptionsRepositoryInterface
{


    /**
     * Save ChecklistItemUserOptions
     * @param \Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsInterface $checklistItemUserOptions
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsInterface $checklistItemUserOptions
    );

    /**
     * Retrieve ChecklistItemUserOptions
     * @param string $checklistitemuseroptionsId
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($checklistitemuseroptionsId);

    /**
     * Retrieve ChecklistItemUserOptions matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete ChecklistItemUserOptions
     * @param \Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsInterface $checklistItemUserOptions
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsInterface $checklistItemUserOptions
    );

    /**
     * Delete ChecklistItemUserOptions by ID
     * @param string $checklistitemuseroptionsId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($checklistitemuseroptionsId);
}
