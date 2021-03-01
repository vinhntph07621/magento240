<?php


namespace Omnyfy\Checklist\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ChecklistItemOptionsRepositoryInterface
{


    /**
     * Save ChecklistItemOptions
     * @param \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface $checklistItemOptions
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface $checklistItemOptions
    );

    /**
     * Retrieve ChecklistItemOptions
     * @param string $checklistitemoptionsId
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($checklistitemoptionsId);

    /**
     * Retrieve ChecklistItemOptions matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete ChecklistItemOptions
     * @param \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface $checklistItemOptions
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface $checklistItemOptions
    );

    /**
     * Delete ChecklistItemOptions by ID
     * @param string $checklistitemoptionsId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($checklistitemoptionsId);
}
