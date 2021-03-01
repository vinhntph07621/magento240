<?php


namespace Omnyfy\MyReadingList\Model\ResourceModel\ReadingList;

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
            'Omnyfy\MyReadingList\Model\ReadingList',
            'Omnyfy\MyReadingList\Model\ResourceModel\ReadingList'
        );
    }

    public function getCustomerList($customer_id){
        $this->getSelect()->where('main_table.user_id = '.$customer_id);
        return $this;
    }
	
	public function isArticleBookMarked($article_id) {
		$this->getSelect()->where('vl.article_id = '.$article_id);
		return $this;
	}

    public function addListArticles()
    {
        //if (!$this->getFlag('has_readinglist_join')) {
            $this->getSelect()->join(
                ['vl'=>$this->getTable('omnyfy_myreadinglists_articles')],
                'vl.readinglist_id = main_table.readinglist_id',
                [
                    'list_id' => 'vl.readinglist_article_id',
                    'article_id' => 'vl.article_id',
                ]
            );
            $this->setFlag('has_readinglist_join', 1);
        //}
        return $this;
    }


    public function populateArticleDetails() {
        //if (!$this->getFlag('has_readinglist_join')) {
        $this->getSelect()->join(
            ['ca'=>$this->getTable('omnyfy_cms_article')],
            'ca.article_id = vl.article_id',
            [
                'topic' => 'ca.title',
                'url-key' => 'ca.identifier',
				'is_active' => 'ca.is_active',
                'updated_time' => 'ca.update_time',
            ]
        )->join(
            ['cac'=>$this->getTable('omnyfy_cms_article_category')],
            'cac.article_id = ca.article_id',
            [
                'category_id' => 'cac.category_id',
            ]
        )->join(
            ['cc'=>$this->getTable('omnyfy_cms_category')],
            'cac.category_id = cc.category_id',
            [
                'title' => 'cc.title',
            ]
        );
        $this->setFlag('has_readinglist_join', 1);
        //}
        return $this;
    }

}
