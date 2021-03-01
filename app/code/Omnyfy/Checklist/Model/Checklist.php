<?php


namespace Omnyfy\Checklist\Model;

use Omnyfy\Checklist\Api\Data\ChecklistInterface;

class Checklist extends \Magento\Framework\Model\AbstractModel implements ChecklistInterface
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\Checklist\Model\ResourceModel\Checklist');
    }

    /**
     * Get checklist_id
     * @return string
     */
    public function getChecklistId()
    {
        return $this->getData(self::CHECKLIST_ID);
    }

    /**
     * Set checklist_id
     * @param string $checklistId
     * @return \Omnyfy\Checklist\Api\Data\ChecklistInterface
     */
    public function setChecklistId($checklistId, $checklist_id)
    {
        return $this->setData(self::CHECKLIST_ID, $checklistId);
    }

    /**
     * Get checklist_title
     * @return string
     */
    public function getChecklistTitle()
    {
        return $this->getData(self::CHECKLIST_TITLE);
    }

    /**
     * Set checklist_title
     * @param string $checklist_title
     * @return \Omnyfy\Checklist\Api\Data\ChecklistInterface
     */
    public function setChecklistTitle($checklist_title)
    {
        return $this->setData(self::CHECKLIST_TITLE, $checklist_title);
    }

    /**
     * Get checklist_description
     * @return string
     */
    public function getChecklistDescription()
    {
        return $this->getData(self::CHECKLIST_DESCRIPTION);
    }

    /**
     * Set checklist_description
     * @param string $checklist_description
     * @return \Omnyfy\Checklist\Api\Data\ChecklistInterface
     */
    public function setChecklistDescription($checklist_description)
    {
        return $this->setData(self::CHECKLIST_DESCRIPTION, $checklist_description);
    }

    /**
     * Get checklist_status
     * @return string
     */
    public function getChecklistStatus()
    {
        return $this->getData(self::CHECKLIST_STATUS);
    }

    /**
     * Set checklist_status
     * @param string $checklist_status
     * @return \Omnyfy\Checklist\Api\Data\ChecklistInterface
     */
    public function setChecklistStatus($checklist_status)
    {
        return $this->setData(self::CHECKLIST_STATUS, $checklist_status);
    }
}
