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
use Magento\Backend\Model\UrlInterface;

/**
 * Class Tree
 *
 * @package Magento\Catalog\Block\Adminhtml\Category
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Tree extends \Magento\Backend\Block\Template {

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

    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\ObjectManagerInterface $objectmanager, \Omnyfy\Cms\Model\Config\Source\CategoryTree $categoryTree, \Magento\Framework\App\Request\Http $request, UrlInterface $backendUrl, Category $category, array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_categoryTree = $categoryTree;
        $this->_objectManager = $objectmanager;
        $this->_backendUrl = $backendUrl;
        $this->request = $request;
        $this->categoryModel = $category;
        parent::__construct($context, $data);
    }

    /**
     * @param mixed|null $parentNodeCategory
     * @param int $recursionLevel
     * @return Node|array|null
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getRoot($parentNodeCategory = null, $recursionLevel = 3) {
        $root = '';
        if ($root === null) {
            $storeId = (int) $this->getRequest()->getParam('store');

            if ($storeId) {
                $store = $this->_storeManager->getStore($storeId);
                $rootId = $store->getRootCategoryId();
            } else {
                $rootId = Category::TREE_ROOT_ID;
            }

            $this->_coreRegistry->register('root', $root);
        }

        return $root;
    }

    public function getCategory() {
        $category = $this->_objectManager->create(Category::class);
        return $category;
    }

    public function getLoadTreeUrl($expanded = null) {
        $params = ['_current' => true, 'id' => null, 'store' => null];

        return $this->getUrl('*/*/categoriesJson', $params);
    }

    public function getTreeJson($parenNodeCategory = null) {
        $json = $this->_jsonEncoder->encode($this->_categoryTree->_getChilds());
        return $json;
    }

    public function getTreeArray($parenNodeCategory = null) {
        $tree = $this->_categoryTree->getTree();
        return $tree;
    }

    public function getSelectedNode() {
        $id = $this->request->getParam('id');
        if ($id) {
            return '#node_' . $id;
        } else if($parent = $this->request->getParam('parent')){
            return '#node_' . $parent;
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getAddRootButtonUrl() {
        return $this->_backendUrl->getUrl('cms/category/new');
    }

    public function getAddSubButtonUrl() {
        if ($this->request->getParam('id')) {
            return $this->_backendUrl->getUrl('cms/category/new', ['parent' => $this->request->getParam('id')]);
        } else {
            return $this->getAddRootButtonUrl();
        }
    }
    
    public function getParentId(){
        if($parent = $this->request->getParam('parent')){
            $parentPath = '';
            $category = $this->categoryModel->load($parent);
            $path = $category->getPath();
            if($path) {
                $parentPath = $path.'/'.$parent;
            } else {
                $parentPath = $parent;
            }
            return $parentPath;
        } else {
            return false;
        }
    }

}
