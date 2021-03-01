<?php


namespace Omnyfy\MyReadingList\Api\Data;

interface ReadingListInterface
{

    const ID = 'id';
    const READINGLIST_ID = 'readinglist_id';


    /**
     * Get readinglist_id
     * @return string|null
     */
    public function getReadinglistId();

    /**
     * Set readinglist_id
     * @param string $readinglist_id
     * @return \Omnyfy\MyReadingList\Api\Data\ReadingListInterface
     */
    public function setReadinglistId($readinglistId);

    /**
     * Get id
     * @return string|null
     */
    public function getId();

    /**
     * Set id
     * @param string $id
     * @return \Omnyfy\MyReadingList\Api\Data\ReadingListInterface
     */
    public function setId($id);
}
