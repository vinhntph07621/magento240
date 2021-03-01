<?php

namespace Omnyfy\Cms\Block\Adminhtml;

/**
 * Admin cms article
 */
class Article extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_article';
        $this->_blockGroup = 'Omnyfy_Cms';
        $this->_headerText = __('Article');
        $this->_addButtonLabel = __('Add New Article');
        parent::_construct();
    }
}
