<?php
/**
 * Project: Strip Subscription
 * User: jing
 * Date: 2019-08-08
 * Time: 00:29
 */
namespace Omnyfy\StripeSubscription\Setup;


use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $conn = $setup->getConnection();

        $tableName = $setup->getTable('omnyfy_stripe_webhook_content');
        if (!$setup->tableExists($tableName)) {
            $responseTable = $conn->newTable($tableName)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                    'Entity ID'
                )
                ->addColumn(
                    'content',
                    Table::TYPE_TEXT,
                    2048,
                    ['nullable' => false],
                    'Data'
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                    'Creation Time'
                )
                ;
            $conn->createTable($responseTable);
        }
    }
}
 