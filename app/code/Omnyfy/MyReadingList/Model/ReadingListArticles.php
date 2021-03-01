<?php


namespace Omnyfy\MyReadingList\Model;

use Omnyfy\MyReadingList\Api\Data\ReadingListArticlesInterface;

class ReadingListArticles extends \Magento\Framework\Model\AbstractModel implements ReadingListArticlesInterface
{
    public function _construct()
    {
        //parent::_construct(); // TODO: Change the autogenerated stub
        $this->_init('Omnyfy\MyReadingList\Model\ResourceModel\ReadingListArticles');
    }

    /**
     * Get readinglist_article_id
     * @return int|null
     */
    public function getId(){
        return $this->getData(self::READINGLIST_ARTICLE_ID);
    }

    /**
     * Get readinglist_id
     * @return string
     */
    public function getReadinglistId()
    {
        return $this->getData(self::READINGLIST_ID);
    }

    /**
     * Set readinglist_id
     * @param string $readinglistId
     * @return \Omnyfy\MyReadingList\Api\Data\ReadingListArticlesInterface
     */
    public function setReadinglistId($readinglistId)
    {
        return $this->setData(self::READINGLIST_ID, $readinglistId);
    }

    /**
     * Get article_id
     * @return string
     */
    public function getArticleId()
    {
        return $this->getData(self::ARTICLE_ID);
    }

    /**
     * Set article_id
     * @param string $articleId
     * @return \Omnyfy\MyReadingList\Api\Data\ReadingListArticlesInterface
     */
    public function setArticleId($articleId)
    {
        return $this->setData(self::ARTICLE_ID, $articleId);
    }

    /**
     * Get added_date
     * @return string
     */
    public function getAddedDate()
    {
        return $this->getData(self::ADDED_DATE);
    }

    /**
     * Set added_date
     * @param string $addedDate
     * @return \Omnyfy\MyReadingList\Api\Data\ReadingListArticlesInterface
     */
    public function setAddedDate($addedDate)
    {
        return $this->setData(self::ADDED_DATE, $addedDate);
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
     * @param string $userId
     * @return \Omnyfy\MyReadingList\Api\Data\ReadingListArticlesInterface
     */
    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
    }
}
