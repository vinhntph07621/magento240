<?php


namespace Omnyfy\Checklist\Api\Data;

interface ChecklistItemsInterface
{

    const CHECKLIST_ITEM_ID = 'checklist_item_id';
    const CHECKLIST_ITEM_DESCRIPTION = 'checklist_item_description';
    const CHECKLISTITEMS_ID = 'checklistitems_id';
    const CHECKLIST_ITEM_TITLE = 'checklist_item_title';
    const CHECKLIST_ITEM_STATUS = 'checklist_item_status';


    /**
     * Get checklistitems_id
     * @return string|null
     */
    public function getChecklistitemsId();

    /**
     * Set checklistitems_id
     * @param string $checklistitems_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemsInterface
     */
    public function setChecklistitemsId($checklistitemsId);

    /**
     * Get checklist_item_id
     * @return string|null
     */
    public function getChecklistItemId();

    /**
     * Set checklist_item_id
     * @param string $checklist_item_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemsInterface
     */
    public function setChecklistItemId($checklist_item_id);

    /**
     * Get checklist_item_title
     * @return string|null
     */
    public function getChecklistItemTitle();

    /**
     * Set checklist_item_title
     * @param string $checklist_item_title
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemsInterface
     */
    public function setChecklistItemTitle($checklist_item_title);

    /**
     * Get checklist_item_description
     * @return string|null
     */
    public function getChecklistItemDescription();

    /**
     * Set checklist_item_description
     * @param string $checklist_item_description
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemsInterface
     */
    public function setChecklistItemDescription($checklist_item_description);

    /**
     * Get checklist_item_status
     * @return string|null
     */
    public function getChecklistItemStatus();

    /**
     * Set checklist_item_status
     * @param string $checklist_item_status
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemsInterface
     */
    public function setChecklistItemStatus($checklist_item_status);
}
