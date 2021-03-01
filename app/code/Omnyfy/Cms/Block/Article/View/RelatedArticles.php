<?php
/**
 * Copyright © 2015 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Block\Article\View;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Cms article related articles block
 */
class RelatedArticles extends \Omnyfy\Cms\Block\Article\ArticleList\AbstractList
{
    /**
     * Prepare articles collection
     *
     * @return void
     */
    protected function _prepareArticleCollection()
    {
        $pageSize = (int) $this->_scopeConfig->getValue(
            'mfcms/article_view/related_articles/number_of_articles',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $this->_articleCollection = $this->getArticle()->getRelatedArticles()
            ->addActiveFilter()
            ->setPageSize($pageSize ?: 5);

        $this->_articleCollection->getSelect()->order('rl.position', 'ASC');
    }

    /**
     * Retrieve true if Display Related Articles enabled
     * @return boolean
     */
    public function displayArticles()
    {
        return (bool) $this->_scopeConfig->getValue(
            'mfcms/article_view/related_articles/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve articles instance
     *
     * @return \Omnyfy\Cms\Model\Category
     */
    public function getArticle()
    {
        if (!$this->hasData('article')) {
            $this->setData('article',
                $this->_coreRegistry->registry('current_cms_article')
            );
        }
        return $this->getData('article');
    }

    /**
     * Get Block Identities
     * @return Array
     */
    public function getIdentities()
    {
        return [\Magento\Cms\Model\Page::CACHE_TAG . '_relatedarticles_'.$this->getArticle()->getId()  ];
    }
}
