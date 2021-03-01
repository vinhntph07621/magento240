<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

/**
 * Categories tree block
 */
namespace Omnyfy\Cms\Block\Adminhtml\Category;

use Omnyfy\Cms\Model\Category;


/**
 * Class Tree
 *
 * @package Magento\Catalog\Block\Adminhtml\Category
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Tree extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'category/tree.phtml';

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendSession;

    /**
     * @var \Magento\Framework\DB\Helper
     */
    protected $_resourceHelper;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;
    
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Tree
     */
    protected $_categoryTree;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var bool
     */
    protected $_withProductCount;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree
     * @param \Magento\Framework\Registry $registry
     * @param CategoryFactory $categoryFactory
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\DB\Helper $resourceHelper
     * @param \Magento\Backend\Model\Auth\Session $backendSession
     * @param array $data
     */
//    public function __construct(
//        \Magento\Backend\Block\Template\Context $context,
//        \Omnyfy\Cms\Model\ResourceModel\Category\Tree $categoryTree,
//        \Magento\Framework\Registry $registry,
//        \Omnyfy\Cms\Model\CategoryFactory $categoryFactory,
//        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
//        \Magento\Framework\DB\Helper $resourceHelper,
//        \Magento\Backend\Model\Auth\Session $backendSession,
//        array $data = []
//    ) {
//        $this->_jsonEncoder = $jsonEncoder;
//        $this->_resourceHelper = $resourceHelper;
//        $this->_backendSession = $backendSession;
//        parent::__construct($context, $data);
//        //parent::__construct($context, $categoryTree, $registry, $categoryFactory, $data);
//    }
    
    public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            \Magento\Framework\Registry $registry,
            \Omnyfy\Cms\Model\ResourceModel\Category\Tree $categoryTree,
            \Magento\Framework\ObjectManagerInterface $objectmanager,
            array $data = []
            ){
        $this->_coreRegistry = $registry;
        $this->_categoryTree = $categoryTree;
        $this->_objectManager = $objectmanager;
        parent::__construct($context, $data);
    }
    
    /**
     * @param mixed|null $parentNodeCategory
     * @param int $recursionLevel
     * @return Node|array|null
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getRoot($parentNodeCategory = null, $recursionLevel = 3)
    {
        $root = $this->_coreRegistry->registry('root');
        if ($root === null) {
            $storeId = (int)$this->getRequest()->getParam('store');

            if ($storeId) {
                $store = $this->_storeManager->getStore($storeId);
                $rootId = $store->getRootCategoryId();
            } else {
                $rootId = Category::TREE_ROOT_ID;
            }

            $tree = $this->_categoryTree->load(null, $recursionLevel);

            if ($this->getCategory()) {
                $tree->loadEnsuredNodes($this->getCategory(), $tree->getNodeById($rootId));
            }

            $tree->addCollectionData($this->getCategoryCollection());

            $root = $tree->getNodeById($rootId);

            if ($root && $rootId != Category::TREE_ROOT_ID) {
                $root->setIsVisible(true);
            } elseif ($root && $root->getId() == Category::TREE_ROOT_ID) {
                $root->setName(__('Root'));
            }

            $this->_coreRegistry->register('root', $root);
        }

        return $root;
    }
    
    public function getCategory()
    {
        $category = $this->_objectManager->create(Category::class);
        return $category;
    }

}
