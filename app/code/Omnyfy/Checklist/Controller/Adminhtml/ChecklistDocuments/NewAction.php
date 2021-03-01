<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 4/4/2018
 * Time: 9:40 AM
 */

namespace Omnyfy\Checklist\Controller\Adminhtml\ChecklistDocuments;

use Omnyfy\Checklist\Controller\Adminhtml\ChecklistDocuments;

class NewAction extends ChecklistDocuments
{
    /**
     * Create new news action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}