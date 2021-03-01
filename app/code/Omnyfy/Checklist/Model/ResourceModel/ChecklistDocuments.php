<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 3/26/2018
 * Time: 9:23 AM
 */

namespace Omnyfy\Checklist\Model\ResourceModel;


class ChecklistDocuments extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('omnyfy_checklist_checklistdocuments', 'checklistdocument_id');
    }
}