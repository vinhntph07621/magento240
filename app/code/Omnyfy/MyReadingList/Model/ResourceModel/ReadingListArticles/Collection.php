<?php


namespace Omnyfy\MyReadingList\Model\ResourceModel\ReadingListArticles;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Omnyfy\MyReadingList\Model\ReadingListArticles',
            'Omnyfy\MyReadingList\Model\ResourceModel\ReadingListArticles'
        );
    }
}
