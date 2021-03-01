<?php


namespace Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUserOptions;

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
            'Omnyfy\Checklist\Model\ChecklistItemUserOptions',
            'Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUserOptions'
        );
    }

    public function joinItemData(){
        $this->getSelect()->join(
            ['ct'=>$this->getTable('omnyfy_checklist_checklistitems')],
            'ct.checklistitems_id = main_table.item_id',
            [
                'checklist_id' => 'ct.checklist_id',
                'checklist_item_title' => 'ct.checklist_item_title',
                'checklist_item_description' => 'ct.checklist_item_description'
            ]
        );
    }

    public function joinItemOptions () {
        $this->getSelect()->join(
        ['cio'=>$this->getTable('omnyfy_checklist_checklistitemoptions')],
        'cio.checklistitemoptions_id = main_table.option_id',
        [
            'item_option_name' => 'cio.name',
        ]
        );
    }

    public function joinCmsArticles(){
        $this->getSelect()->join(
            ['ca'=>$this->getTable('omnyfy_cms_article')],
            'ca.article_id = cio.cms_article_link',
            [
                'article_url' => 'ca.identifier',
                'article_title' => 'ca.title'
            ]
        );
        $this->getSelect()->where('ca.is_active = 1');
        $this->setFlag('checklistitems_join', 1);
    }
}
