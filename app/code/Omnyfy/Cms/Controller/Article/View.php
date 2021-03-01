<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Controller\Article;

/**
 * Cms article view
 */
class View extends \Magento\Framework\App\Action\Action
{

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
    }

    /**
     * View Cms article action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $article = $this->_initArticle();
        if (!$article) {
            $this->_forward('index', 'noroute', 'cms');
            return;
        }

        $this->_objectManager->get('\Magento\Framework\Registry')
            ->register('current_cms_article', $article);
        $resultPage = $this->_objectManager->get('Omnyfy\Cms\Helper\Page')
            ->prepareResultPage($this, $article);
        return $resultPage;
    }

    /**
     * Init Article
     *
     * @return \Omnyfy\Cms\Model\Article || false
     */
    protected function _initArticle()
    {
        $id = $this->getRequest()->getParam('id');
        $storeId = $this->_storeManager->getStore()->getId();

        $article = $this->_objectManager->create('Omnyfy\Cms\Model\Article')->load($id);
		
		$article->setArticleCounter($article->getArticleCounter()+1);
                $article->setArticleCounterUpdate(1);
		$article->save();
		
        if (!$article->isVisibleOnStore($storeId)) {
            return false;
        }

        $article->setStoreId($storeId);

        return $article;
    }

}
