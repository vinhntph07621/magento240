<?php


namespace Omnyfy\Checklist\Api\Data;

interface ChecklistItemUserUploadsInterface
{

    const CHECKLISTITEMUSERUPLOADS_ID = 'checklistitemuseruploads_id';
    const ITEM_ID = 'item_id';
    const UPLOAD_LINK = 'upload_link';
    const USER_ID = 'user_id';
    const UPLOAD_IS = 'upload_is';


    /**
     * Get checklistitemuseruploads_id
     * @return string|null
     */
    public function getChecklistitemuseruploadsId();

    /**
     * Set checklistitemuseruploads_id
     * @param string $checklistitemuseruploads_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsInterface
     */
    public function setChecklistitemuseruploadsId($checklistitemuseruploadsId);

    /**
     * Get upload_is
     * @return string|null
     */
    public function getUploadIs();

    /**
     * Set upload_is
     * @param string $upload_is
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsInterface
     */
    public function setUploadIs($upload_is);

    /**
     * Get user_id
     * @return string|null
     */
    public function getUserId();

    /**
     * Set user_id
     * @param string $user_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsInterface
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
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsInterface
     */
    public function setItemId($item_id);

    /**
     * Get upload_link
     * @return string|null
     */
    public function getUploadLink();

    /**
     * Set upload_link
     * @param string $upload_link
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsInterface
     */
    public function setUploadLink($upload_link);
}
