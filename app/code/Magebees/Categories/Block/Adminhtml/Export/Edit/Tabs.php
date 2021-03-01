<?php
namespace Magebees\Categories\Block\Adminhtml\Export\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('export_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Export Categories'));
    }
    protected function _prepareLayout()
    {
        
        $this->addTab(
            'export_section',
            [
                'label' => __('Export Information'),
                'title' => __('Export Information'),
                'content' => $this->getLayout()->createBlock(
                    'Magebees\Categories\Block\Adminhtml\Export\Edit\Tab\Export'
                )->toHtml(),
                'active' => true
            ]
        );
        
        return parent::_prepareLayout();
    }
}
