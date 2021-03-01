<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 4/4/2018
 * Time: 9:35 AM
 */

namespace Omnyfy\Checklist\Block\Adminhtml\ChecklistDocuments\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('doc_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('View'));
    }

    protected function _beforeToHtml()
    {
        try {
            $this->addTab(
                'view_documents',
                [
                    'label' => __('View Documents'),
                    'title' => __('View Documents'),
                    'content' => null,
                    'active' => true
                ]
            );
            
        }catch (\Exception $exception){
        }

        return parent::_beforeToHtml();
    }
}