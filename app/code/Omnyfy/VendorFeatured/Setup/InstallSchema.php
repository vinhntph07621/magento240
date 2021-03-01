<?php


namespace Omnyfy\VendorFeatured\Setup;

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

        $table_omnyfy_vendorfeatured_vendor_featured = $setup->getConnection()->newTable($setup->getTable('omnyfy_vendorfeatured_vendor_featured'));

        
        $table_omnyfy_vendorfeatured_vendor_featured->addColumn(
            'vendor_featured_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,'auto_increment' => true),
            'Entity ID'
        );
        

        
        $table_omnyfy_vendorfeatured_vendor_featured->addColumn(
            'vendor_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => False],
            'Vendor Id'
        );
        

        
        $table_omnyfy_vendorfeatured_vendor_featured->addColumn(
            'added_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => False],
            'Added Date'
        );
        

        
        $table_omnyfy_vendorfeatured_vendor_featured->addColumn(
            'updated_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => False],
            'Updated Date'
        );
        

        $table_omnyfy_vendorfeatured_vendor_featured_tag = $setup->getConnection()->newTable($setup->getTable('omnyfy_vendorfeatured_vendor_featured_tag'));

        
        $table_omnyfy_vendorfeatured_vendor_featured_tag->addColumn(
            'vendor_featured_tag_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        

        
        $table_omnyfy_vendorfeatured_vendor_featured_tag->addColumn(
            'vendor_featured_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => False],
            'Vendor Featured Id'
        );
        

        
        $table_omnyfy_vendorfeatured_vendor_featured_tag->addColumn(
            'vendor_tag_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => False],
            'Tag Id'
        );
        

        $table_omnyfy_vendorfeatured_vendor_tag = $setup->getConnection()->newTable($setup->getTable('omnyfy_vendorfeatured_vendor_tag'));

        
        $table_omnyfy_vendorfeatured_vendor_tag->addColumn(
            'vendor_tag_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        

        
        $table_omnyfy_vendorfeatured_vendor_tag->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Name'
        );
        

        $setup->getConnection()->createTable($table_omnyfy_vendorfeatured_vendor_tag);

        $setup->getConnection()->createTable($table_omnyfy_vendorfeatured_vendor_featured_tag);

        $setup->getConnection()->createTable($table_omnyfy_vendorfeatured_vendor_featured);

        $setup->endSetup();
    }
}
