<?php


namespace Omnyfy\Checklist\Model;

use Omnyfy\Checklist\Api\Data\ChecklistItemsInterface;

class ChecklistItems extends \Magento\Framework\Model\AbstractModel implements ChecklistItemsInterface
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\Checklist\Model\ResourceModel\ChecklistItems');
    }

    /**
     * Get checklistitems_id
     * @return string
     */
    public function getChecklistitemsId()
    {
        return $this->getData(self::CHECKLISTITEMS_ID);
    }

    /**
     * Set checklistitems_id
     * @param string $checklistitemsId
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemsInterface
     */
    public function setChecklistitemsId($checklistitemsId)
    {
        return $this->setData(self::CHECKLISTITEMS_ID, $checklistitemsId);
    }

    /**
     * Get checklist_item_id
     * @return string
     */
    public function getChecklistItemId()
    {
        return $this->getData(self::CHECKLIST_ITEM_ID);
    }

    /**
     * Set checklist_item_id
     * @param string $checklist_item_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemsInterface
     */
    public function setChecklistItemId($checklist_item_id)
    {
        return $this->setData(self::CHECKLIST_ITEM_ID, $checklist_item_id);
    }

    /**
     * Get checklist_item_title
     * @return string
     */
    public function getChecklistItemTitle()
    {
        return $this->getData(self::CHECKLIST_ITEM_TITLE);
    }

    /**
     * Set checklist_item_title
     * @param string $checklist_item_title
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemsInterface
     */
    public function setChecklistItemTitle($checklist_item_title)
    {
        return $this->setData(self::CHECKLIST_ITEM_TITLE, $checklist_item_title);
    }

    /**
     * Get checklist_item_description
     * @return string
     */
    public function getChecklistItemDescription()
    {
        return $this->getData(self::CHECKLIST_ITEM_DESCRIPTION);
    }

    /**
     * Set checklist_item_description
     * @param string $checklist_item_description
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemsInterface
     */
    public function setChecklistItemDescription($checklist_item_description)
    {
        return $this->setData(self::CHECKLIST_ITEM_DESCRIPTION, $checklist_item_description);
    }

    /**
     * Get checklist_item_status
     * @return string
     */
    public function getChecklistItemStatus()
    {
        return $this->getData(self::CHECKLIST_ITEM_STATUS);
    }

    /**
     * Set checklist_item_status
     * @param string $checklist_item_status
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemsInterface
     */
    public function setChecklistItemStatus($checklist_item_status)
    {
        return $this->setData(self::CHECKLIST_ITEM_STATUS, $checklist_item_status);
    }
}
