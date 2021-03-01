<?php


namespace Omnyfy\Checklist\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ChecklistRepositoryInterface
{


    /**
     * Save Checklist
     * @param \Omnyfy\Checklist\Api\Data\ChecklistInterface $checklist
     * @return \Omnyfy\Checklist\Api\Data\ChecklistInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Omnyfy\Checklist\Api\Data\ChecklistInterface $checklist
    );

    /**
     * Retrieve Checklist
     * @param string $checklistId
     * @return \Omnyfy\Checklist\Api\Data\ChecklistInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($checklistId);

    /**
     * Retrieve Checklist matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Omnyfy\Checklist\Api\Data\ChecklistSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Checklist
     * @param \Omnyfy\Checklist\Api\Data\ChecklistInterface $checklist
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Omnyfy\Checklist\Api\Data\ChecklistInterface $checklist
    );

    /**
     * Delete Checklist by ID
     * @param string $checklistId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($checklistId);
}
