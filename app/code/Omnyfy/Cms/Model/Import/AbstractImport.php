<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Model\Import;

/**
 * Abstract import model
 */
abstract class AbstractImport extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Connect to bd
     */
    protected $_connect;

    /**
     * @var array
     */
    protected $_requiredFields = [];

    /**
     * @var \Omnyfy\Cms\Model\ArticleFactory
     */
    protected $_articleFactory;

    /**
     * @var \Omnyfy\Cms\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var integer
     */
    protected $_importedArticlesCount = 0;

    /**
     * @var integer
     */
    protected $_importedCategoriesCount = 0;

    /**
     * @var array
     */
    protected $_skippedArticles = [];

    /**
     * @var array
     */
    protected $_skippedCategories = [];

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Omnyfy\Cms\Model\ArticleFactory $articleFactory,
     * @param \Omnyfy\Cms\Model\CategoryFactory $categoryFactory,
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager,
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Omnyfy\Cms\Model\ArticleFactory $articleFactory,
        \Omnyfy\Cms\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_articleFactory = $articleFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_storeManager = $storeManager;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve import statistic
     * @return \Magento\Framework\DataObject
     */
    public function getImportStatistic()
    {
        return new \Magento\Framework\DataObject([
            'imported_articles_count'      => $this->_importedArticlesCount,
            'imported_categories_count' => $this->_importedCategoriesCount,
            'skipped_articles'             => $this->_skippedArticles,
            'skipped_categories'        => $this->_skippedCategories,
            'imported_count'            => $this->_importedArticlesCount + $this->_importedCategoriesCount,
            'skipped_count'              => count($this->_skippedArticles) + count($this->_skippedCategories),
        ]);
    }

    /**
     * Prepare import data
     * @param  array $data
     * @return $this
     */
    public function prepareData($data)
    {
        if (!is_array($data)) {
            $data = (array) $data;
        }

        foreach($this->_requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \Exception(__('Parameter %1 is required', $field), 1);
            }
        }

        foreach($data as $field => $value) {
            if (!in_array($field, $this->_requiredFields)) {
                unset($data[$field]);
            }
        }

        $this->setData($data);

        return $this;
    }

    /**
     * Execute mysql query
     */
    protected function _mysqliQuery($sql)
    {
        $result = mysqli_query($this->_connect, $sql);
        if (!$result) {
            throw new \Exception(
                __('Mysql error: %1.', mysqli_error($this->_connect))
            );
        }

        return $result;
    }
}
