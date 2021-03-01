<?php

namespace Omnyfy\Checklist\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class ChecklistDocuments extends Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        //$this->_controller = 'adminhtml_chechlist_documents';
        $this->_blockGroup = 'Checklist';
        $this->_headerText = __('Checklist Documents');
        parent::_construct();
    }
}
