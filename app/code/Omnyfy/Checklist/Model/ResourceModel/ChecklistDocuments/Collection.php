<?php

namespace Omnyfy\Checklist\Model\ResourceModel\ChecklistDocuments;


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
            'Omnyfy\Checklist\Model\ChecklistDocuments',
            'Omnyfy\Checklist\Model\ResourceModel\ChecklistDocuments'
        );
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->join(
            ['user' => $this->getTable('customer_entity')],
            'main_table.user_id = user.entity_id',
            [
                'customer_email' => 'user.email',
                'customer_name' => "CONCAT(user.firstname, ' ', user.lastname)"
            ]
        );


        $this->getSelect()->join(
            ['cev' => $this->getTable('customer_entity_varchar')],
            'user.entity_id = cev.entity_id',
            [
                'customer_mobile' => 'cev.value',
            ]
        );

        $this->getSelect()->where('cev.attribute_id = 200');



        $this->getSelect()->join(
            ['checklist' => $this->getTable('omnyfy_checklist_checklist')],
            'main_table.checklist_id = checklist.checklist_id',
            [
                'checklist_title' => 'checklist.checklist_title'
            ]
        );
    }
}