<?php


namespace Omnyfy\VendorAuth\Setup;

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

        $table_omnyfy_vendorauth_log = $setup->getConnection()->newTable($setup->getTable('omnyfy_vendorauth_log'));


        $table_omnyfy_vendorauth_log->addColumn(
            'log_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );



        $table_omnyfy_vendorauth_log->addColumn(
            'loggedin_vendor_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'loggedin_vendor_id'
        );



        $table_omnyfy_vendorauth_log->addColumn(
            'module',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'module'
        );



        $table_omnyfy_vendorauth_log->addColumn(
            'route',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'route'
        );



        $table_omnyfy_vendorauth_log->addColumn(
            'controller',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'controller'
        );



        $table_omnyfy_vendorauth_log->addColumn(
            'action',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'action'
        );



        $table_omnyfy_vendorauth_log->addColumn(
            'requested_entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'requested_entity_id'
        );



        $table_omnyfy_vendorauth_log->addColumn(
            'date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'date'
        );


        $setup->getConnection()->createTable($table_omnyfy_vendorauth_log);

        $setup->endSetup();
    }
}