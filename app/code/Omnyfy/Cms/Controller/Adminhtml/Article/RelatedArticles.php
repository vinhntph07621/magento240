<?php

namespace Omnyfy\Cms\Controller\Adminhtml\Article;

/**
 * Cms article related articles controller
 */
class RelatedArticles extends \Omnyfy\Cms\Controller\Adminhtml\Article
{
    /**
     * View related articles action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $model = $this->_getModel();
        $this->_getRegistry()->register('current_model', $model);

        $this->_view->loadLayout()
            ->getLayout()
            ->getBlock('cms.article.edit.tab.relatedarticles')
            ->setArticlesRelated($this->getRequest()->getArticle('articles_related', null));

        $this->_view->renderLayout();
    }
}
