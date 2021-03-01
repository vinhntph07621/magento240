<?php


namespace Omnyfy\Checklist\Model\ResourceModel;

class ChecklistItemUserUploads extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('omnyfy_checklist_checklistitemuseruploads', 'checklistitemuseruploads_id');
    }
}
