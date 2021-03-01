<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 3/26/2018
 * Time: 9:58 AM
 */

namespace Omnyfy\Checklist\Model;


class ChecklistDocuments extends \Magento\Framework\Model\AbstractModel
{
    const CHECKLISTDOCUMENT_ID = 'checklistitemuseruploads_id';
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\Checklist\Model\ResourceModel\ChecklistDocuments');
    }

    /**
     * Get checklistitemoptions_id
     * @return string
     */
    public function getChecklistDocumentId()
    {
        return $this->getData(self::CHECKLISTDOCUMENT_ID);
    }

    /**
     * Set checklistitemoptions_id
     * @param string $checklistitemoptionsId
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface
     */
    public function setChecklistDocumentId($checklistDocumentId)
    {
        return $this->setData(self::CHECKLISTDOCUMENT_ID, $checklistDocumentId);
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
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface
     */
    public function setOptionId($option_id)
    {
        return $this->setData(self::OPTION_ID, $option_id);
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
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface
     */
    public function setItemId($item_id)
    {
        return $this->setData(self::ITEM_ID, $item_id);
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set name
     * @param string $name
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get cms_article_link
     * @return string
     */
    public function getCmsArticleLink()
    {
        return $this->getData(self::CMS_ARTICLE_LINK);
    }

    /**
     * Set cms_article_link
     * @param string $cms_article_link
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface
     */
    public function setCmsArticleLink($cms_article_link)
    {
        return $this->setData(self::CMS_ARTICLE_LINK, $cms_article_link);
    }

    /**
     * Get upload_name
     * @return string
     */
    public function getUploadName()
    {
        return $this->getData(self::UPLOAD_NAME);
    }

    /**
     * Set upload_name
     * @param string $upload_name
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface
     */
    public function setUploadName($upload_name)
    {
        return $this->setData(self::UPLOAD_NAME, $upload_name);
    }

    /**
     * Get upload_item_id
     * @return string
     */
    public function getUploadItemId()
    {
        return $this->getData(self::UPLOAD_ITEM_ID);
    }

    /**
     * Set upload_item_id
     * @param string $upload_item_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface
     */
    public function setUploadItemId($upload_item_id)
    {
        return $this->setData(self::UPLOAD_ITEM_ID, $upload_item_id);
    }
}