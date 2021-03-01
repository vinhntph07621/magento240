<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */
namespace Omnyfy\Cms\Block\Article;

use Magento\Store\Model\ScopeInterface;

/**
 * Cms article view
 */
class View extends AbstractArticle
{
    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $article = $this->getArticle();
        if ($article) {
            $this->_addBreadcrumbs($article);
            $this->pageConfig->addBodyClass('cms-article-' . $article->getIdentifier());
            $this->pageConfig->getTitle()->set($article->getMetaTitle());
            $this->pageConfig->setKeywords($article->getMetaKeywords());
            $this->pageConfig->setDescription($article->getMetaDescription());
            $this->pageConfig->addRemotePageAsset(
                $article->getArticleUrl(),
                'canonical',
                ['attributes' => ['rel' => 'canonical']]
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Prepare breadcrumbs
     *
     * @param \Omnyfy\Cms\Model\Article $article
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs(\Omnyfy\Cms\Model\Article $article)
    {
        if ($this->_scopeConfig->getValue('web/default/show_cms_breadcrumbs', ScopeInterface::SCOPE_STORE)
            && ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs'))
        ) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'cms',
                [
                    'label' => $this->_scopeConfig->getValue('mfcms/index_page/title', ScopeInterface::SCOPE_STORE),
                    'title' => __('Go to Cms Home Page'),
                    'link' => $this->_url->getBaseUrl()
                ]
            );
            $breadcrumbsBlock->addCrumb('cms_article', [
                'label' => $article->getTitle(),
                'title' => $article->getTitle()
            ]);
        }
    }

}
