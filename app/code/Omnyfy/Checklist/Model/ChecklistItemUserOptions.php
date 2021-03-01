<?php


namespace Omnyfy\Checklist\Model;

use Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsInterface;

class ChecklistItemUserOptions extends \Magento\Framework\Model\AbstractModel implements ChecklistItemUserOptionsInterface
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUserOptions');
    }

    /**
     * Get checklistitemuseroptions_id
     * @return string
     */
    public function getChecklistitemuseroptionsId()
    {
        return $this->getData(self::CHECKLISTITEMUSEROPTIONS_ID);
    }

    /**
     * Set checklistitemuseroptions_id
     * @param string $checklistitemuseroptionsId
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsInterface
     */
    public function setChecklistitemuseroptionsId($checklistitemuseroptionsId)
    {
        return $this->setData(self::CHECKLISTITEMUSEROPTIONS_ID, $checklistitemuseroptionsId);
    }

    /**
     * Get user_option_id
     * @return string
     */
    public function getUserOptionId()
    {
        return $this->getData(self::USER_OPTION_ID);
    }

    /**
     * Set user_option_id
     * @param string $user_option_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsInterface
     */
    public function setUserOptionId($user_option_id)
    {
        return $this->setData(self::USER_OPTION_ID, $user_option_id);
    }

    /**
     * Get user_id
     * @return string
     */
    public function getUserId()
    {
        return $this->getData(self::USER_ID);
    }

    /**
     * Set user_id
     * @param string $user_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsInterface
     */
    public function setUserId($user_id)
    {
        return $this->setData(self::USER_ID, $user_id);
    }

    /**
     * Get item_id
     * @return string
     */
    public function getItemId()
    {
        return $this->getData(self::ITEM_ID);
    }

    /**
     * Set item_id
     * @param string $item_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsInterface
     */
    public function setItemId($item_id)
    {
        return $this->setData(self::ITEM_ID, $item_id);
    }

    /**
     * Get option_id
     * @return string
     */
    public function getOptionId()
    {
        return $this->getData(self::OPTION_ID);
    }

    /**
     * Set option_id
     * @param string $option_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsInterface
     */
    public function setOptionId($option_id)
    {
        return $this->setData(self::OPTION_ID, $option_id);
    }
}
