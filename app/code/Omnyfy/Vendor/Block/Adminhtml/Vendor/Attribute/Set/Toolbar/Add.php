<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-16
 * Time: 15:38
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Vendor\Attribute\Set\Toolbar;

class Add extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Omnyfy_Vendor::vendor/attribute/set/toolbar/add.phtml';

    /**
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        if ($this->getToolbar()) {
            $this->getToolbar()->addChild(
                'save_button',
                'Magento\Backend\Block\Widget\Button',
                [
                    'label' => __('Save'),
                    'class' => 'save primary save-attribute-set',
                    'data_attribute' => [
                        'mage-init' => ['button' => ['event' => 'save', 'target' => '#set-prop-form']],
                    ]
                ]
            );
            $this->getToolbar()->addChild(
                'back_button',
                'Magento\Backend\Block\Widget\Button',
                [
                    'label' => __('Back'),
                    'onclick' => 'setLocation(\'' . $this->getUrl('omnyfy_vendor/*/') . '\')',
                    'class' => 'back'
                ]
            );
        }

        $this->addChild('setForm', 'Omnyfy\Vendor\Block\Adminhtml\Vendor\Attribute\Set\Main\Formset');
        return parent::_prepareLayout();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function _getHeader()
    {
        return __('Add New Attribute Set');
    }

    /**
     * @return string
     */
    public function getFormHtml()
    {
        return $this->getChildHtml('setForm');
    }

    /**
     * Return id of form, used by this block
     *
     * @return string
     */
    public function getFormId()
    {
        return $this->getChildBlock('setForm')->getForm()->getId();
    }
}
 