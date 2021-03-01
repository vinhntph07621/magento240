<?php


namespace Omnyfy\Checklist\Api\Data;

interface ChecklistItemOptionsInterface
{

    const ITEM_ID = 'item_id';
    const OPTION_ID = 'option_id';
    const UPLOAD_NAME = 'upload_name';
    const NAME = 'name';
    const CHECKLISTITEMOPTIONS_ID = 'checklistitemoptions_id';
    const CMS_ARTICLE_LINK = 'cms_article_link';
    const UPLOAD_ITEM_ID = 'upload_item_id';


    /**
     * Get checklistitemoptions_id
     * @return string|null
     */
    public function getChecklistitemoptionsId();

    /**
     * Set checklistitemoptions_id
     * @param string $checklistitemoptions_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface
     */
    public function setChecklistitemoptionsId($checklistitemoptionsId);

    /**
     * Get option_id
     * @return string|null
     */
    public function getOptionId();

    /**
     * Set option_id
     * @param string $option_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface
     */
    public function setOptionId($option_id);

    /**
     * Get item_id
     * @return string|null
     */
    public function getItemId();

    /**
     * Set item_id
     * @param string $item_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface
     */
    public function setItemId($item_id);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface
     */
    public function setName($name);

    /**
     * Get cms_article_link
     * @return string|null
     */
    public function getCmsArticleLink();

    /**
     * Set cms_article_link
     * @param string $cms_article_link
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface
     */
    public function setCmsArticleLink($cms_article_link);

    /**
     * Get upload_name
     * @return string|null
     */
    public function getUploadName();

    /**
     * Set upload_name
     * @param string $upload_name
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface
     */
    public function setUploadName($upload_name);

    /**
     * Get upload_item_id
     * @return string|null
     */
    public function getUploadItemId();

    /**
     * Set upload_item_id
     * @param string $upload_item_id
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface
     */
    public function setUploadItemId($upload_item_id);
}
