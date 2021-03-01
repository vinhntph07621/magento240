<?php


namespace Omnyfy\Checklist\Model\ResourceModel\ChecklistItems;

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
            'Omnyfy\Checklist\Model\ChecklistItems',
            'Omnyfy\Checklist\Model\ResourceModel\ChecklistItems'
        );
    }

    public function joinItemData(){
        $this->getSelect()->join(
            ['ciu'=>$this->getTable('omnyfy_checklist_checklistitemuploads')],
            'ciu.item_id = main_table.checklistitems_id',
            [
                'upload_name' => 'ciu.upload_name',
                'checklistitemuploads_id' => 'ciu.checklistitemuploads_id'
            ]
        );
    }
}
