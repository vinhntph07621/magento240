<?php


namespace Omnyfy\MyReadingList\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $table_omnyfy_myreadinglists = $setup->getConnection()->newTable($setup->getTable('omnyfy_myreadinglists'));


        $table_omnyfy_myreadinglists->addColumn(
            'readinglist_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        $table_omnyfy_myreadinglists->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('nullable' => false),
            'Owner ID'
        );

        $table_omnyfy_myreadinglists->addColumn(
            'added_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            array('nullable' => false),
            'Date Article List Crated'
        );
        $setup->getConnection()->createTable($table_omnyfy_myreadinglists);
        $setup->endSetup();

        $omnyfy_myreadinglists_articles = $setup->getConnection()->newTable($setup->getTable('omnyfy_myreadinglists_articles'));


        $omnyfy_myreadinglists_articles->addColumn(
            'readinglist_article_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        $omnyfy_myreadinglists_articles->addColumn(
            'readinglist_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('nullable' => false),
            'Reading List ID'
        );
        $omnyfy_myreadinglists_articles->addColumn(
            'article_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('nullable' => false),
            'Article Id'
        );
        $omnyfy_myreadinglists_articles->addColumn(
            'added_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            array('nullable' => false),
            'Date Article Saved'
        );
        $setup->getConnection()->createTable($omnyfy_myreadinglists_articles);

        $setup->endSetup();
    }
}
