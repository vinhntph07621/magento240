<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Block\Widget;

/**
 * Cms recent articles widget
 */
class Recent extends \Omnyfy\Cms\Block\Article\ArticleList\AbstractList implements \Magento\Widget\Block\BlockInterface
{

    /**
     * @var \Omnyfy\Cms\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Omnyfy\Cms\Model\Category
     */
    protected $_category;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Omnyfy\Cms\Model\ResourceModel\Article\CollectionFactory $articleCollectionFactory
     * @param \Omnyfy\Cms\Model\Url $url
     * @param \Omnyfy\Cms\Model\CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Omnyfy\Cms\Model\ResourceModel\Article\CollectionFactory $articleCollectionFactory,
        \Omnyfy\Cms\Model\Url $url,
        \Omnyfy\Cms\Model\CategoryFactory $categoryFactory,
        array $data = []
    ) {
        parent::__construct($context, $coreRegistry, $filterProvider, $articleCollectionFactory, $url, $data);
        $this->_categoryFactory = $categoryFactory;
    }

    /**
     * Set cms template
     *
     * @return this
     */
    public function _toHtml()
    {
        $this->setTemplate(
            $this->getData('custom_template') ?: 'widget/recent.phtml'
        );

        return parent::_toHtml();
    }

    /**
     * Retrieve block title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('title') ?: __('Recent Cms Articles');
    }

    /**
     * Prepare articles collection
     *
     * @return void
     */
    protected function _prepareArticleCollection()
    {
        $size = $this->getData('number_of_articles');
        if (!$size) {
            $size = (int) $this->_scopeConfig->getValue(
                'mfcms/sidebar/recent_articles/articles_per_page',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }

        $this->setPageSize($size);

        parent::_prepareArticleCollection();

        if ($category = $this->getCategory()) {
            $categories = $category->getChildrenIds();
            $categories[] = $category->getId();
            $this->_articleCollection->addCategoryFilter($categories);
        }
    }

    /**
     * Retrieve category instance
     *
     * @return \Omnyfy\Cms\Model\Category
     */
    public function getCategory()
    {
        if ($this->_category === null) {
            if ($categoryId = $this->getData('category_id')) {
                $category = $this->_categoryFactory->create();
                $category->load($categoryId);

                $storeId = $this->_storeManager->getStore()->getId();
                if ($category->isVisibleOnStore($storeId)) {
                    $category->setStoreId($storeId);
                    return $this->_category = $category;
                }
            }

            $this->_category = false;
        }

        return $this->_category;
    }

    /**
     * Retrieve article short content
     * @param  \Omnyfy\Cms\Model\Article $article
     *
     * @return string
     */
    public function getShorContent($article)
    {
        $content = $article->getContent();
        $pageBraker = '<!-- pagebreak -->';

        if ($p = mb_strpos($content, $pageBraker)) {
            $content = mb_substr($content, 0, $p);
        }

        $content = $this->_filterProvider->getPageFilter()->filter($content);

        $dom = new \DOMDocument();
        $dom->loadHTML($content);
        $content = $dom->saveHTML();

        return $content;
    }
}
