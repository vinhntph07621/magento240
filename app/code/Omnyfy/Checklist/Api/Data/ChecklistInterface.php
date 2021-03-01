<?php


namespace Omnyfy\Checklist\Api\Data;

interface ChecklistInterface
{

    const CHECKLIST_TITLE = 'checklist_title';
    const CHECKLIST_ID = 'checklist_id';
    const CHECKLIST_STATUS = 'checklist_status';
    const CHECKLIST_DESCRIPTION = 'checklist_description';


    /**
     * Get checklist_id
     * @return string|null
     */
    public function getChecklistId();

    /**
     * Set checklist_id
     * @param string $checklist_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistInterface
     */
    public function setChecklistId($checklistId, $checklist_id);

    /**
     * Get checklist_title
     * @return string|null
     */
    public function getChecklistTitle();

    /**
     * Set checklist_title
     * @param string $checklist_title
     * @return \Omnyfy\Checklist\Api\Data\ChecklistInterface
     */
    public function setChecklistTitle($checklist_title);

    /**
     * Get checklist_description
     * @return string|null
     */
    public function getChecklistDescription();

    /**
     * Set checklist_description
     * @param string $checklist_description
     * @return \Omnyfy\Checklist\Api\Data\ChecklistInterface
     */
    public function setChecklistDescription($checklist_description);

    /**
     * Get checklist_status
     * @return string|null
     */
    public function getChecklistStatus();

    /**
     * Set checklist_status
     * @param string $checklist_status
     * @return \Omnyfy\Checklist\Api\Data\ChecklistInterface
     */
    public function setChecklistStatus($checklist_status);
}
