<?php


namespace Omnyfy\MyReadingList\Model\ResourceModel;

class ReadingListArticles extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('omnyfy_myreadinglists_articles', 'readinglist_article_id');
    }
}
