<?php
/**
 * Copyright © 2015 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Controller\Adminhtml\Article;

/**
 * Cms article related products controller
 */
class RelatedProducts extends \Omnyfy\Cms\Controller\Adminhtml\Article
{
    /**
     * View related products action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $model = $this->_getModel();
        $this->_getRegistry()->register('current_model', $model);

        $this->_view->loadLayout()
            ->getLayout()
            ->getBlock('cms.article.edit.tab.relatedproducts')
            ->setProductsRelated($this->getRequest()->getArticle('products_related', null));

        $this->_view->renderLayout();
    }
}
