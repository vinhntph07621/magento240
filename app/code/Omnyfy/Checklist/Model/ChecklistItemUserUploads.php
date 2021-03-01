<?php


namespace Omnyfy\Checklist\Model;

use Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsInterface;

class ChecklistItemUserUploads extends \Magento\Framework\Model\AbstractModel implements ChecklistItemUserUploadsInterface
{
    
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUserUploads');
    }

    /**
     * Get checklistitemuseruploads_id
     * @return string
     */
    public function getChecklistitemuseruploadsId()
    {
        return $this->getData(self::CHECKLISTITEMUSERUPLOADS_ID);
    }

    /**
     * Set checklistitemuseruploads_id
     * @param string $checklistitemuseruploadsId
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsInterface
     */
    public function setChecklistitemuseruploadsId($checklistitemuseruploadsId)
    {
        return $this->setData(self::CHECKLISTITEMUSERUPLOADS_ID, $checklistitemuseruploadsId);
    }

    /**
     * Get upload_is
     * @return string
     */
    public function getUploadIs()
    {
        return $this->getData(self::UPLOAD_IS);
    }

    /**
     * Set upload_is
     * @param string $upload_is
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsInterface
     */
    public function setUploadIs($upload_is)
    {
        return $this->setData(self::UPLOAD_IS, $upload_is);
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
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsInterface
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
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsInterface
     */
    public function setItemId($item_id)
    {
        return $this->setData(self::ITEM_ID, $item_id);
    }

    /**
     * Get upload_link
     * @return string
     */
    public function getUploadLink()
    {
        return $this->getData(self::UPLOAD_LINK);
    }

    /**
     * Set upload_link
     * @param string $upload_link
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsInterface
     */
    public function setUploadLink($upload_link)
    {
        return $this->setData(self::UPLOAD_LINK, $upload_link);
    }
}
