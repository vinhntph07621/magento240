<?php
namespace Magebees\Categories\Block\Adminhtml\Export;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magebees_Categories';
        $this->_controller = 'adminhtml_export';

        parent::_construct();

        $this->buttonList->remove('save', 'label', __('Export Categories'));
        $this->buttonList->remove('back');
        $this->buttonList->remove('reset');
        
        $this->addButton(
            'exportbutton',
            [
                'label' => __('Export Categories'),
                'onclick' => 'exportData()',
                'class' => 'scalable exportbutton primary',
                'level' => -1
            ]
        );
            
            
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
