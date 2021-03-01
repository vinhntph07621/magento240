<?php


namespace Omnyfy\Checklist\Model\ResourceModel\Checklist;

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
            'Omnyfy\Checklist\Model\Checklist',
            'Omnyfy\Checklist\Model\ResourceModel\Checklist'
        );
    }

    protected function _getItemId(\Magento\Framework\DataObject $item)
    {
        if ($this->getFlag('checklistitems_join') && $this->getFlag('checklistitemoptions_join')) {
            return $item->getId() . '_' . $item->getData('checklistitems_id') . '_' . $item->getData('checklistitemoptions_id');
        }else{
            return $item->getId();
        }
    }

    public function joinChecklistItems(){
        $this->getSelect()->join(
            ['ci'=>$this->getTable('omnyfy_checklist_checklistitems')],
            'ci.checklist_id = main_table.checklist_id',
            [
                'checklistitems_id' => 'ci.checklistitems_id',
                'checklist_item_title' => 'ci.checklist_item_title',
                'checklist_item_description' => 'ci.checklist_item_description',
                'checklist_item_order' => 'ci.checklist_item_order'
            ]
        );
        $this->setFlag('checklistitems_join', 1);
    }

    public function joinChecklistItemOptions(){
        $this->getSelect()->join(
            ['cio'=>$this->getTable('omnyfy_checklist_checklistitemoptions')],
            'cio.item_id = ci.checklistitems_id',
            [
                'checklistitemoptions_id' => 'cio.checklistitemoptions_id',
                'name' => 'cio.name',
                'cms_article_link' => 'cio.cms_article_link'
            ]
        );
        $this->setFlag('checklistitemoptions_join', 1);
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
