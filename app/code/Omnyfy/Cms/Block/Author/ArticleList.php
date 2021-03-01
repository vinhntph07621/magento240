<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Block\Author;

use Magento\Store\Model\ScopeInterface;

/**
 * Cms author articles list
 */
class ArticleList extends \Omnyfy\Cms\Block\Article\ArticleList
{
    /**
     * Prepare articles collection
     *
     * @return void
     */
    protected function _prepareArticleCollection()
    {
        parent::_prepareArticleCollection();
        if ($author = $this->getAuthor()) {
            $this->_articleCollection->addAuthorFilter($author);
        }
    }

    /**
     * Retrieve author instance
     *
     * @return \Omnyfy\Cms\Model\Author
     */
    public function getAuthor()
    {
        return $this->_coreRegistry->registry('current_cms_author');
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        if ($author = $this->getAuthor()) {
            $this->_addBreadcrumbs($author);
            $this->pageConfig->addBodyClass('cms-author-' . $author->getIdentifier());
            $this->pageConfig->getTitle()->set($author->getTitle());
            $this->pageConfig->addRemotePageAsset(
                $author->getAuthorUrl(),
                'canonical',
                ['attributes' => ['rel' => 'canonical']]
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Prepare breadcrumbs
     *
     * @param \Omnyfy\Cms\Model\Author $author
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs($author)
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

            $breadcrumbsBlock->addCrumb('cms_author',[
                'label' => $author->getTitle(),
                'title' => $author->getTitle()
            ]);
        }
    }
}
