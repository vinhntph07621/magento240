<?php


namespace Omnyfy\VendorSearch\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
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

        $table_omnyfy_vendorsearch_searchhistory = $setup->getConnection()->newTable($setup->getTable('omnyfy_vendorsearch_searchhistory'));

        
        $table_omnyfy_vendorsearch_searchhistory->addColumn(
            'searchhistory_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        

        
        $table_omnyfy_vendorsearch_searchhistory->addColumn(
            'location',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Suburb or Postcode'
        );
        

        
        $table_omnyfy_vendorsearch_searchhistory->addColumn(
            'attribute_value_id_1',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'attribute_value_id_1'
        );
        

        
        $table_omnyfy_vendorsearch_searchhistory->addColumn(
            'attribute_value_1',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'attribute_value_1'
        );
        

        
        $table_omnyfy_vendorsearch_searchhistory->addColumn(
            'attribute_value_id_2',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'attribute_value_id_2'
        );
        

        
        $table_omnyfy_vendorsearch_searchhistory->addColumn(
            'search_string',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'search_string'
        );
        

        
        $table_omnyfy_vendorsearch_searchhistory->addColumn(
            'search_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['default' => 'Table::TIMESTAMP_INIT'],
            'Search Date'
        );
        

        
        $table_omnyfy_vendorsearch_searchhistory->addColumn(
            'attribute_value_2',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'attribute_value_2'
        );
        

        $setup->getConnection()->createTable($table_omnyfy_vendorsearch_searchhistory);

        $setup->endSetup();
    }
}
