<?php

namespace Omnyfy\Cms\Block\Adminhtml\Article\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

/**
 * Admin cms article edit form related articles tab
 */
class RelatedArticles extends Extended implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\Article\Attribute\Source\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\Article\Attribute\Source\Status $status
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_status = $status;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('related_articles_section');
        $this->setDefaultSort('article_id');
        $this->setUseAjax(true);
        if ($this->getArticle() && $this->getArticle()->getId()) {
            $this->setDefaultFilter(['in_articles' => 1]);
        }
        if ($this->isReadonly()) {
            $this->setFilterVisibility(false);
        }
    }

    /**
     * Retrieve currently edited article model
     *
     * @return array|null
     */
    public function getArticle()
    {
        return $this->_coreRegistry->registry('current_model');
    }

    /**
     * Add filter
     *
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in article flag
        if ($column->getId() == 'in_articles') {
            $articleIds = $this->_getSelectedArticles();
            if (empty($articleIds)) {
                $articleIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('article_id', ['in' => $articleIds]);
            } else {
                if ($articleIds) {
                    $this->getCollection()->addFieldToFilter('article_id', ['nin' => $articleIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare collection
     *
     * @return Extended
     */
    protected function _prepareCollection()
    {
        $article = $this->getArticle();
        $collection = $article->getCollection()
        	->addFieldToFilter('article_id', array('neq' => $article->getId()));


        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Checks when this block is readonly
     *
     * @return bool
     */
    public function isReadonly()
    {
        return false;
    }

    /**
     * Add columns to grid
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        if (!$this->isReadonly()) {
            $this->addColumn(
                'in_articles',
                [
                    'type' => 'checkbox',
                    'name' => 'in_articles',
                    'values' => $this->_getSelectedArticles(),
                    'align' => 'center',
                    'index' => 'article_id',
                    'header_css_class' => 'col-select',
                    'column_css_class' => 'col-select'
                ]
            );
        }

        $this->addColumn(
            'article_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'article_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'index' => 'title',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );

        $this->addColumn(
            'identifier',
            [
                'header' => __('URL Key'),
                'index' => 'identifier',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'store_id',
                [
                    'header' => __('Store View'),
                    'index' => 'store_id',
                    'type' => 'store',
                    'store_all' => true,
                    'store_view' => true,
                    'sortable' => false,
                ]
            );
        }

        $this->addColumn(
            'is_active',
            [
                'header' => __('Status'),
                'index' => 'is_active',
                'type' => 'options',
                'options' => $this->_status->getOptionArray(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status',
                'frame_callback' => array(
                    $this->getLayout()->createBlock('Omnyfy\Cms\Block\Adminhtml\Grid\Column\Statuses'),
                    'decorateStatus'
                ),
            ]
        );

        $this->addColumn(
            'position',
            [
                'header' => __('Position'),
                'name' => 'position',
                'type' => 'number',
                'validate_class' => 'validate-number',
                'index' => 'position',
                'editable' => true,
                'edit_only' => false,
                'sortable' => false,
                'filter' => false,
                'header_css_class' => 'col-position',
                'column_css_class' => 'col-position'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getData(
            'grid_url'
        ) ? $this->getData(
            'grid_url'
        ) : $this->getUrl(
            'cms/article/relatedArticlesGrid',
            ['_current' => true]
        );
    }

    /**
     * Retrieve selected related articles
     *
     * @return array
     */
    protected function _getSelectedArticles()
    {
        $articles = $this->getArticlesRelated();
        if (!is_array($articles)) {
            $articles = array_keys($this->getSelectedRelatedArticles());
        }
        return $articles;
    }

    /**
     * Retrieve related articles
     *
     * @return array
     */
    public function getSelectedRelatedArticles()
    {
        $articles = [];
        foreach ($this->_coreRegistry->registry('current_model')->getRelatedArticles() as $article) {
            $articles[$article->getId()] = ['position' => $article->getPosition()];
        }
        return $articles;
    }


    public function getTabLabel()
    {
        return __('Related Articles');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Related Articles');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
