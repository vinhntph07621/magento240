<?php


namespace Omnyfy\Checklist\Model;

use Omnyfy\Checklist\Api\Data\ChecklistItemUploadsInterface;

class ChecklistItemUploads extends \Magento\Framework\Model\AbstractModel implements ChecklistItemUploadsInterface
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUploads');
    }

    /**
     * Get checklistitemuploads_id
     * @return string
     */
    public function getChecklistitemuploadsId()
    {
        return $this->getData(self::CHECKLISTITEMUPLOADS_ID);
    }

    /**
     * Set checklistitemuploads_id
     * @param string $checklistitemuploadsId
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUploadsInterface
     */
    public function setChecklistitemuploadsId($checklistitemuploadsId)
    {
        return $this->setData(self::CHECKLISTITEMUPLOADS_ID, $checklistitemuploadsId);
    }

    /**
     * Get upload_id
     * @return string
     */
    public function getUploadId()
    {
        return $this->getData(self::UPLOAD_ID);
    }

    /**
     * Set upload_id
     * @param string $upload_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUploadsInterface
     */
    public function setUploadId($upload_id)
    {
        return $this->setData(self::UPLOAD_ID, $upload_id);
    }
}
