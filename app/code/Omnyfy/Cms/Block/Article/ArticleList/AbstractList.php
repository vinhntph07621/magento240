<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Block\Article\ArticleList;

use Magento\Store\Model\ScopeInterface;

/**
 * Abstract cms article list block
 */
abstract class AbstractList extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_article;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Omnyfy\Cms\Model\ResourceModel\Article\CollectionFactory
     */
    protected $_articleCollectionFactory;

    /**
     * @var \Omnyfy\Cms\Model\ResourceModel\Article\Collection
     */
    protected $_articleCollection;

    /**
     * @var \Omnyfy\Cms\Model\Url
     */
    protected $_url;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Omnyfy\Cms\Model\ResourceModel\Article\CollectionFactory $articleCollectionFactory
     * @param \Omnyfy\Cms\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Omnyfy\Cms\Model\ResourceModel\Article\CollectionFactory $articleCollectionFactory,
        \Omnyfy\Cms\Model\Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_filterProvider = $filterProvider;
        $this->_articleCollectionFactory = $articleCollectionFactory;
        $this->_url = $url;
    }

    /**
     * Prepare articles collection
     *
     * @return void
     */
    protected function _prepareArticleCollection()
    {
        $this->_articleCollection = $this->_articleCollectionFactory->create()
            ->addActiveFilter()
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('publish_time', 'DESC');

        if ($this->getPageSize()) {
            $this->_articleCollection->setPageSize($this->getPageSize());
        }
    }

    /**
     * Prepare articles collection
     *
     * @return \Omnyfy\Cms\Model\ResourceModel\Article\Collection
     */
    public function getArticleCollection()
    {
        if (is_null($this->_articleCollection)) {
            $this->_prepareArticleCollection();
        }

        return $this->_articleCollection;
    }

}
