<?php


namespace Omnyfy\Checklist\Api\Data;

interface ChecklistItemUserOptionsInterface
{

    const ITEM_ID = 'item_id';
    const CHECKLISTITEMUSEROPTIONS_ID = 'checklistitemuseroptions_id';
    const OPTION_ID = 'option_id';
    const USER_ID = 'user_id';
    const USER_OPTION_ID = 'user_option_id';


    /**
     * Get checklistitemuseroptions_id
     * @return string|null
     */
    public function getChecklistitemuseroptionsId();

    /**
     * Set checklistitemuseroptions_id
     * @param string $checklistitemuseroptions_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsInterface
     */
    public function setChecklistitemuseroptionsId($checklistitemuseroptionsId);

    /**
     * Get user_option_id
     * @return string|null
     */
    public function getUserOptionId();

    /**
     * Set user_option_id
     * @param string $user_option_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsInterface
     */
    public function setUserOptionId($user_option_id);

    /**
     * Get user_id
     * @return string|null
     */
    public function getUserId();

    /**
     * Set user_id
     * @param string $user_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsInterface
     */
    public function setUserId($user_id);

    /**
     * Get item_id
     * @return string|null
     */
    public function getItemId();

    /**
     * Set item_id
     * @param string $item_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsInterface
     */
    public function setItemId($item_id);

    /**
     * Get option_id
     * @return string|null
     */
    public function getOptionId();

    /**
     * Set option_id
     * @param string $option_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsInterface
     */
    public function setOptionId($option_id);
}
