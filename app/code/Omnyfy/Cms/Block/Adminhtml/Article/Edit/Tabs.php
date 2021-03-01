<?php

namespace Omnyfy\Cms\Block\Adminhtml\Article\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('article_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Article Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab(
            'related_articles_section',
            [
                'label' => __('Related Articles1'),
                'url' => $this->getUrl('cms/article/relatedArticles', ['_current' => true]),
                'class' => 'ajax',
            ]
        );

        $this->addTab(
            'related_products_section',
            [
                'label' => __('Related Products'),
                'url' => $this->getUrl('cms/article/relatedProducts', ['_current' => true]),
                'class' => 'ajax',
            ]
        );
        return parent::_beforeToHtml();
    }
}
