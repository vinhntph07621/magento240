<?php


namespace Omnyfy\Checklist\Api\Data;

interface ChecklistItemUploadsInterface
{

    const UPLOAD_ID = 'upload_id';
    const CHECKLISTITEMUPLOADS_ID = 'checklistitemuploads_id';


    /**
     * Get checklistitemuploads_id
     * @return string|null
     */
    public function getChecklistitemuploadsId();

    /**
     * Set checklistitemuploads_id
     * @param string $checklistitemuploads_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUploadsInterface
     */
    public function setChecklistitemuploadsId($checklistitemuploadsId);

    /**
     * Get upload_id
     * @return string|null
     */
    public function getUploadId();

    /**
     * Set upload_id
     * @param string $upload_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUploadsInterface
     */
    public function setUploadId($upload_id);
}
