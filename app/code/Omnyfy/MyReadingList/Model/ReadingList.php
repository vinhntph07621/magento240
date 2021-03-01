<?php


namespace Omnyfy\MyReadingList\Model;

use Omnyfy\MyReadingList\Api\Data\ReadingListInterface;

class ReadingList extends \Magento\Framework\Model\AbstractModel implements ReadingListInterface
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\MyReadingList\Model\ResourceModel\ReadingList');
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
     * @return \Omnyfy\MyReadingList\Api\Data\ReadingListInterface
     */
    public function setReadinglistId($readinglistId)
    {
        return $this->setData(self::READINGLIST_ID, $readinglistId);
    }

    /**
     * Get id
     * @return string
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set id
     * @param string $id
     * @return \Omnyfy\MyReadingList\Api\Data\ReadingListInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }
}
