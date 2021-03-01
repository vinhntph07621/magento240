<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Block\Tag;

use Magento\Store\Model\ScopeInterface;

/**
 * Cms tag articles list
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
        if ($tag = $this->getTag()) {
            $this->_articleCollection->addTagFilter($tag);
        }
    }

    /**
     * Retrieve tag instance
     *
     * @return \Omnyfy\Cms\Model\Tag
     */
    public function getTag()
    {
        return $this->_coreRegistry->registry('current_cms_tag');
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        if ($tag = $this->getTag()) {
            $this->_addBreadcrumbs($tag);
            $this->pageConfig->addBodyClass('cms-tag-' . $tag->getIdentifier());
            $this->pageConfig->getTitle()->set($tag->getTitle());
            $this->pageConfig->addRemotePageAsset(
                $tag->getTagUrl(),
                'canonical',
                ['attributes' => ['rel' => 'canonical']]
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Prepare breadcrumbs
     *
     * @param \Omnyfy\Cms\Model\Tag $tag
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs($tag)
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

            $breadcrumbsBlock->addCrumb('cms_tag',[
                'label' => $tag->getTitle(),
                'title' => $tag->getTitle()
            ]);
        }
    }
}
