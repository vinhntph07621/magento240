<?php

namespace Omnyfy\Cms\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Cms setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'omnyfy_cms_article'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('omnyfy_cms_article')
        )->addColumn(
            'article_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Article ID'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Article Title'
        )->addColumn(
            'meta_keywords',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Article Meta Keywords'
        )->addColumn(
            'meta_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Article Meta Description'
        )->addColumn(
            'identifier',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => true, 'default' => null],
            'Article String Identifier'
        )->addColumn(
            'content_heading',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Article Content Heading'
        )->addColumn(
            'content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Article Content'
        )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Article Creation Time'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Article Modification Time'
        )->addColumn(
            'publish_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Article Publish Time'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is Article Active'
        )->addIndex(
            $installer->getIdxName('omnyfy_cms_article', ['identifier']),
            ['identifier']
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('omnyfy_cms_article'),
                ['title', 'meta_keywords', 'meta_description', 'identifier', 'content'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['title', 'meta_keywords', 'meta_description', 'identifier', 'content'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'Omnyfy Cms Article Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'omnyfy_cms_article_store'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('omnyfy_cms_article_store')
        )->addColumn(
            'article_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Article ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $installer->getIdxName('omnyfy_cms_article_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('omnyfy_cms_article_store', 'article_id', 'omnyfy_cms_article', 'article_id'),
            'article_id',
            $installer->getTable('omnyfy_cms_article'),
            'article_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('omnyfy_cms_article_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Omnyfy Cms Article To Store Linkage Table'
        );
        $installer->getConnection()->createTable($table);


        $table = $installer->getConnection()->newTable(
            $installer->getTable('omnyfy_cms_category')
        )->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Category ID'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Category Title'
        )->addColumn(
            'meta_keywords',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Category Meta Keywords'
        )->addColumn(
            'meta_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Category Meta Description'
        )->addColumn(
            'identifier',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => true, 'default' => null],
            'Category String Identifier'
        )->addColumn(
            'content_heading',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Category Content Heading'
        )->addColumn(
            'content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Category Content'
        )->addColumn(
            'path',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Category Path'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Category Position'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is Category Active'
        )->addIndex(
            $installer->getIdxName('omnyfy_cms_category', ['identifier']),
            ['identifier']
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('omnyfy_cms_category'),
                ['title', 'meta_keywords', 'meta_description', 'identifier', 'content'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['title', 'meta_keywords', 'meta_description', 'identifier', 'content'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'Omnyfy Cms Category Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'omnyfy_cms_category_store'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('omnyfy_cms_category_store')
        )->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Category ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $installer->getIdxName('omnyfy_cms_category_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('omnyfy_cms_category_store', 'category_id', 'omnyfy_cms_category', 'category_id'),
            'category_id',
            $installer->getTable('omnyfy_cms_category'),
            'category_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('omnyfy_cms_category_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Omnyfy Cms Category To Store Linkage Table'
        );
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'omnyfy_cms_article_category'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('omnyfy_cms_article_category')
        )->addColumn(
            'article_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Article ID'
        )->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Category ID'
        )->addIndex(
            $installer->getIdxName('omnyfy_cms_article_category', ['category_id']),
            ['category_id']
        )->addForeignKey(
            $installer->getFkName('omnyfy_cms_article_category', 'article_id', 'omnyfy_cms_article', 'article_id'),
            'article_id',
            $installer->getTable('omnyfy_cms_article'),
            'article_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('omnyfy_cms_article_category', 'category_id', 'omnyfy_cms_category', 'category_id'),
            'category_id',
            $installer->getTable('omnyfy_cms_category'),
            'category_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Omnyfy Cms Article To Category Linkage Table'
        );
        $installer->getConnection()->createTable($table);
        

        /**
         * Create table 'omnyfy_cms_article_relatedproduct'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('omnyfy_cms_article_relatedproduct')
        )->addColumn(
            'article_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Article ID'
        )->addColumn(
            'related_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Related Product ID'
        )->addIndex(
            $installer->getIdxName('omnyfy_cms_article_relatedproduct', ['related_id']),
            ['related_id']
        )->addForeignKey(
            $installer->getFkName('omnyfy_cms_article_relatedproduct', 'article_id', 'omnyfy_cms_article', 'article_id'),
            'article_id',
            $installer->getTable('omnyfy_cms_article'),
            'article_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('omnyfy_cms_article_relatedproduct', 'related_id', 'catalog_product_entity', 'entity_id'),
            'related_id',
            $installer->getTable('catalog_product_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Omnyfy Cms Article To Product Linkage Table'
        );
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'omnyfy_cms_article_relatedproduct'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('omnyfy_cms_article_relatedarticle')
        )->addColumn(
            'article_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Article ID'
        )->addColumn(
            'related_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Related Article ID'
        )->addIndex(
            $installer->getIdxName('omnyfy_cms_article_relatedproduct', ['related_id']),
            ['related_id']
        )->addForeignKey(
            $installer->getFkName('omnyfy_cms_article_relatedproduct1', 'article_id', 'omnyfy_cms_article', 'article_id'),
            'article_id',
            $installer->getTable('omnyfy_cms_article'),
            'article_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('omnyfy_cms_article_relatedproduct2', 'related_id', 'omnyfy_cms_article', 'article_id'),
            'article_id',
            $installer->getTable('omnyfy_cms_article'),
            'article_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Omnyfy Cms Article To Article Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
