<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Block\Article\View;

use Magento\Store\Model\ScopeInterface;

/**
 * Cms article next and prev article links
 */
class NextPrev extends \Magento\Framework\View\Element\Template
{
    /**
     * Previous article
     *
     * @var \Omnyfy\Cms\Model\Article
     */
    protected $_prevArticle;

    /**
     * Next article
     *
     * @var \Omnyfy\Cms\Model\Article
     */
    protected $_nextArticle;

    /**
     * @var \Omnyfy\Cms\Model\ResourceModel\Article\CollectionFactory
     */
    protected $_articleCollectionFactory;

    /**
     * @var Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Omnyfy\Cms\Model\ResourceModel\Article\CollectionFactory $_tagCollectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Omnyfy\Cms\Model\ResourceModel\Article\CollectionFactory $articleCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_articleCollectionFactory = $articleCollectionFactory;
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Retrieve true if need to display next-prev links
     *
     * @return boolean
     */
    public function displayLinks()
    {
        return (bool)$this->_scopeConfig->getValue(
            'mfcms/article_view/nextprev/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve prev article
     * @return \Omnyfy\Cms\Model\Article || bool
     */
    public function getPrevArticle()
    {
        if ($this->_prevArticle === null) {
            $this->_prevArticle = false;
            $collection = $this->_getFrontendCollection()->addFieldToFilter(
                'publish_time', [
                    'gteq' => $this->getArticle()->getPublishTime()
                ]
            );
            $article = $collection->getFirstItem();

            if ($article->getId()) {
                $this->_prevArticle = $article;
            }
        }

        return $this->_prevArticle;
    }

    /**
     * Retrieve next article
     * @return \Omnyfy\Cms\Model\Article || bool
     */
    public function getNextArticle()
    {
        if ($this->_nextArticle === null) {
            $this->_nextArticle = false;
            $collection = $this->_getFrontendCollection()->addFieldToFilter(
                'publish_time', [
                    'lteq' => $this->getArticle()->getPublishTime()
                ]
            );
            $article = $collection->getFirstItem();

            if ($article->getId()) {
                $this->_nextArticle = $article;
            }
        }

        return $this->_nextArticle;
    }

    /**
     * Retrieve article collection with frontend filters and order
     * @return bool
     */
    protected function _getFrontendCollection()
    {
        $collection = $this->_articleCollectionFactory->create();
        $collection->addActiveFilter()
            ->addFieldToFilter('article_id', ['neq' => $this->getArticle()->getId()])
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('publish_time', 'DESC')
            ->setPageSize(1);
        return $collection;
    }

    /**
     * Retrieve article instance
     *
     * @return \Omnyfy\Cms\Model\Article
     */
    public function getArticle()
    {
        return $this->_coreRegistry->registry('current_cms_article');
    }

}
