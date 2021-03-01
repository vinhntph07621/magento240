<?php


namespace Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUploads;

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
            'Omnyfy\Checklist\Model\ChecklistItemUploads',
            'Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUploads'
        );
    }
}
