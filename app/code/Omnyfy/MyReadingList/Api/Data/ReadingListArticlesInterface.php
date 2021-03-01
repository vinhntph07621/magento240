<?php


namespace Omnyfy\MyReadingList\Api\Data;

interface ReadingListArticlesInterface
{
    const READINGLIST_ARTICLE_ID = 'readinglist_article_id';
    const READINGLIST_ID = 'readinglist_id';
    const ADDED_DATE = 'added_date';
    const USER_ID = 'user_id';
    const ARTICLE_ID = 'article_id';

    /**
     * Get readinglist_article_id
     * @return int|null
     */
    public function getId();

    /**
     * Get readinglist_id
     * @return string|null
     */
    public function getReadinglistId();

    /**
     * Set readinglist_id
     * @param string $readinglistId
     * @return \Omnyfy\MyReadingList\Api\Data\ReadingListArticlesInterface
     */
    public function setReadinglistId($readinglistId);

    /**
     * Get article_id
     * @return string|null
     */
    public function getArticleId();

    /**
     * Set article_id
     * @param string $articleId
     * @return \Omnyfy\MyReadingList\Api\Data\ReadingListArticlesInterface
     */
    public function setArticleId($articleId);

    /**
     * Get added_date
     * @return string|null
     */
    public function getAddedDate();

    /**
     * Set added_date
     * @param string $addedDate
     * @return \Omnyfy\MyReadingList\Api\Data\ReadingListArticlesInterface
     */
    public function setAddedDate($addedDate);

    /**
     * Get user_id
     * @return string|null
     */
    public function getUserId();

    /**
     * Set user_id
     * @param string $userId
     * @return \Omnyfy\MyReadingList\Api\Data\ReadingListArticlesInterface
     */
    public function setUserId($userId);
}