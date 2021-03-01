<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-11
 * Time: 17:46
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Vendor\Attribute\Set\Toolbar;

class Main extends \Magento\Backend\Block\Template
{
    //parent for grid, no need to customise
    protected $_template = 'Magento_Catalog::catalog/product/attribute/set/toolbar/main.phtml';

    protected function _prepareLayout()
    {
        $this->getToolbar()->addChild(
            'addButton',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Add Attribute Set'),
                'onclick' => 'setLocation(\'' . $this->getUrl('omnyfy_vendor/*/add') . '\')',
                'class' => 'add primary add-set'
            ]
        );

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getNewButtonHtml()
    {
        return $this->getChildHtml('addButton');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function _getHeader()
    {
        return __('Attribute Sets');
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $this->_eventManager->dispatch(
            'omnyfy_vendor_vendor_attribute_set_toolbar_main_html_before',
            ['block' => $this]
        );
        return parent::_toHtml();
    }
}
 