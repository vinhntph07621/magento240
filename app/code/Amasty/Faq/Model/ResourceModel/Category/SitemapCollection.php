<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Model\ResourceModel\Category;

use Amasty\Faq\Api\Data\CategoryInterface;
use Amasty\Faq\Model\ConfigProvider;
use Amasty\Faq\Setup\Operation\CreateCategoryStoreTable;
use Amasty\Faq\Setup\Operation\CreateCategoryTable;
use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;

class SitemapCollection extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        ConfigProvider $configProvider,
        $connectionName = null
    ) {
        $this->storeManager = $storeManager;
        $this->configProvider = $configProvider;
        parent::__construct($context, $connectionName);
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CreateCategoryTable::TABLE_NAME, CategoryInterface::CATEGORY_ID);
    }

    /**
     * @param $storeId
     * @return array|bool
     */
    public function getCollection($storeId)
    {
        $categories = [];

        $store = $this->storeManager->getStore($storeId);

        if (!$store) {
            return false;
        }

        $urlKey = $this->configProvider->getUrlKey();

        $connection = $this->getConnection();

        $select = $connection->select()
            ->from(
                ['e' => $this->getTable(CreateCategoryTable::TABLE_NAME)],
                [CategoryInterface::CATEGORY_ID, CategoryInterface::URL_KEY, CategoryInterface::UPDATED_AT]
            )
            ->joinLeft(
                ['st1' => $this->getTable(CreateCategoryStoreTable::TABLE_NAME)],
                'e.category_id = st1.category_id AND st1.store_id = 0',
                null
            )
            ->joinLeft(
                ['st2' => $this->getTable(CreateCategoryStoreTable::TABLE_NAME)],
                'e.category_id = st2.category_id AND st2.store_id = ' . $storeId,
                null
            )
            ->where('e.exclude_sitemap = 0');

        $query = $connection->query($select);
        while ($row = $query->fetch()) {
            $category = $this->prepareCategory($row, $urlKey);
            $categories[$category->getId()] = $category;
        }

        return $categories;
    }

    /**
     * @param array $categoryRow
     * @param $urlKey
     * @return DataObject
     */
    protected function prepareCategory(array $categoryRow, $urlKey)
    {
        $category = new DataObject();
        $category->setId($categoryRow[CategoryInterface::CATEGORY_ID]);
        $categoryUrl = $urlKey . '/' . $categoryRow[CategoryInterface::URL_KEY] . '/';
        $category->setUrl($categoryUrl);
        $category->setUpdatedAt($categoryRow[CategoryInterface::UPDATED_AT]);

        return $category;
    }
}
