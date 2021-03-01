<?php
namespace Magebees\Categories\Block\Adminhtml\Import;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magebees_Categories';
        $this->_controller = 'adminhtml_import';

        parent::_construct();
        $this->buttonList->remove('back');
        $this->buttonList->remove('save');
                
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('block_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'categories_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'categories_content');
                }
            }
		";
    }
}
