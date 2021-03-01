<?php

namespace Omnyfy\Cms\Block;

use Magento\Store\Model\ScopeInterface;

/**
 * Cms index block
 */
class Index extends \Omnyfy\Cms\Block\Article\ArticleList
{
    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->_addBreadcrumbs();
        $this->pageConfig->getTitle()->set($this->_getConfigValue('title'));
        $this->pageConfig->setKeywords($this->_getConfigValue('meta_keywords'));
        $this->pageConfig->setDescription($this->_getConfigValue('meta_description'));
        $this->pageConfig->addRemotePageAsset(
            $this->_url->getBaseUrl(),
            'canonical',
            ['attributes' => ['rel' => 'canonical']]
        );

        return parent::_prepareLayout();
    }

    /**
     * Prepare breadcrumbs
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs()
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
                    'title' => __(sprintf('Go to Cms Home Page'))
                ]
            );
        }
    }

    /**
     * Retrieve cms title
     * @return string
     */
    protected function _getConfigValue($param)
    {
        return $this->_scopeConfig->getValue(
            'mfcms/index_page/'.$param,
            ScopeInterface::SCOPE_STORE
        );
    }

}
