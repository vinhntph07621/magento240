<?php


namespace Omnyfy\Checklist\Model\ResourceModel\ChecklistItemOptions;

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
            'Omnyfy\Checklist\Model\ChecklistItemOptions',
            'Omnyfy\Checklist\Model\ResourceModel\ChecklistItemOptions'
        );
    }

    public function joinCmsArticles(){
        $this->getSelect()->join(
            ['ca'=>$this->getTable('omnyfy_cms_article')],
            'ca.article_id = main_table.cms_article_link',
            [
                'article_url' => 'ca.identifier',
                'article_title' => 'ca.title'
            ]
        );
        $this->getSelect()->where('ca.is_active = 1');
        $this->setFlag('checklistitems_join', 1);
    }

    public function isChecked($userId){
        $this->getSelect()->join(
            ['cuo'=>$this->getTable('omnyfy_checklist_checklistitemuseroptions')],
            'cuo.option_id = main_table.checklistitemoptions_id AND cuo.user_id = '.$userId,
            [
                'checklistitemuseroptions_id' => 'cuo.checklistitemuseroptions_id',
                'checklist_item_id' => 'cuo.item_id'
            ]
        );
    }
}
