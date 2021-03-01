<?php


namespace Omnyfy\Checklist\Model\ResourceModel;

class ChecklistItemOptions extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('omnyfy_checklist_checklistitemoptions', 'checklistitemoptions_id');
    }
}
