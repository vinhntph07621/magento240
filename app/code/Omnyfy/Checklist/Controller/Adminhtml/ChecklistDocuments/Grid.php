<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 4/3/2018
 * Time: 5:40 PM
 */

namespace Omnyfy\Checklist\Controller\Adminhtml\ChecklistDocuments;

use Omnyfy\Checklist\Controller\Adminhtml\ChecklistDocuments;

class Grid extends ChecklistDocuments
{
    /**
     * @return void
     */
    public function execute()
    {
        return $this->_resultPageFactory->create();
    }
}