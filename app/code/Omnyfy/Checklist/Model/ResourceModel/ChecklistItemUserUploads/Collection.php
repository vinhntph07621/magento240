<?php


namespace Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUserUploads;

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
            'Omnyfy\Checklist\Model\ChecklistItemUserUploads',
            'Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUserUploads'
        );
    }

    public function joinChecklistItems(){
        $this->getSelect()->join(
            ['ci'=>$this->getTable('omnyfy_checklist_checklistitems')],
            'ci.checklistitems_id = main_table.item_id',
            [
                'checklistitems_id' => 'ci.checklistitems_id',
                'checklist_item_title' => 'ci.checklist_item_title',
                'checklist_item_description' => 'ci.checklist_item_description',
                'checklist_item_order' => 'ci.checklist_item_order'
            ]
        );
    }

    public function joinChecklistItemUploads(){
        $this->getSelect()->join(
            ['ciu'=>$this->getTable('omnyfy_checklist_checklistitemuploads')],
            'ciu.item_id = main_table.item_id',
            [
                'upload_name' => 'ciu.upload_name'
            ]
        );
    }
}
