<?php


namespace Omnyfy\Checklist\Model\ResourceModel;

class ChecklistItemUploads extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('omnyfy_checklist_checklistitemuploads', 'checklistitemuploads_id');
    }
}
