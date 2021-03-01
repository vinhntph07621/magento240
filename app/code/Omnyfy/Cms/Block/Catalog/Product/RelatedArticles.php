<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Block\Catalog\Product;

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
            'mfcms/product_page/number_of_related_articles',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!$pageSize) {
            $pageSize = 5;
        }
        $this->setPageSize($pageSize);

        parent::_prepareArticleCollection();

        $product = $this->getProduct();
        $this->_articleCollection->getSelect()->joinLeft(
            ['rl' => $product->getResource()->getTable('omnyfy_cms_article_relatedproduct')],
            'main_table.article_id = rl.article_id',
            ['position']
        )->where(
            'rl.related_id = ?',
            $product->getId()
        );
    }

    /**
     * Retrieve true if Display Related Articles enabled
     * @return boolean
     */
    public function displayArticles()
    {
        return (bool) $this->_scopeConfig->getValue(
            'mfcms/product_page/related_articles_enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve articles instance
     *
     * @return \Omnyfy\Cms\Model\Category
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product',
                $this->_coreRegistry->registry('current_product')
            );
        }
        return $this->getData('product');
    }

    /**
     * Get Block Identities
     * @return Array
     */
    public function getIdentities()
    {
        return [\Magento\Catalog\Model\Product::CACHE_TAG . '_relatedarticles_'.$this->getArticle()->getId()  ];
    }
}
