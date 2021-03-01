<?php


namespace Omnyfy\Checklist\Model;

use Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface;

class ChecklistItemOptions extends \Magento\Framework\Model\AbstractModel implements ChecklistItemOptionsInterface
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\Checklist\Model\ResourceModel\ChecklistItemOptions');
    }

    /**
     * Get checklistitemoptions_id
     * @return string
     */
    public function getChecklistitemoptionsId()
    {
        return $this->getData(self::CHECKLISTITEMOPTIONS_ID);
    }

    /**
     * Set checklistitemoptions_id
     * @param string $checklistitemoptionsId
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface
     */
    public function setChecklistitemoptionsId($checklistitemoptionsId)
    {
        return $this->setData(self::CHECKLISTITEMOPTIONS_ID, $checklistitemoptionsId);
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
