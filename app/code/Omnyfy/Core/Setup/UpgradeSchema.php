<?php
/**
 * Project: Omnyfy Core.
 * Date: 6/10/17
 * Time: 10:39 AM
 */
namespace Omnyfy\Core\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $version = $context->getVersion();
        if (version_compare($version, '1.0.1', '<')) {
            if (!$setup->tableExists('omnyfy_core_queue')) {
                $queueTable = $setup->getConnection()->newTable(
                    $setup->getTable('omnyfy_core_queue')
                )
                    ->addColumn(
                        'id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                        'ID'
                    )
                    ->addColumn(
                        'topic',
                        Table::TYPE_TEXT,
                        80,
                        ['nullable' => false, 'default' => ''],
                        'Queue Type'
                    )
                    ->addColumn(
                        'message',
                        Table::TYPE_TEXT,
                        255,
                        ['nullable' => false, 'default' => '0'],
                        'Queue Message'
                    )
                    ->addColumn(
                        'status',
                        Table::TYPE_TEXT,
                        32,
                        ['nullable' => false, 'default' => 'pending'],
                        'Status'
                    )
                    ->addIndex(
                        'IDX_QUEUE_TYPE',
                        ['topic'],
                        ['type' => AdapterInterface::INDEX_TYPE_INDEX]
                    )
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8')
                ;
                $setup->getConnection()->createTable($queueTable);
            }

            if ($setup->tableExists('omnyfy_queue') && !$setup->tableExists('omnyfy_core_queue')) {
                $setup->getConnection()->renameTable('omnyfy_queue', 'omnyfy_core_queue');
            }

            if ($setup->tableExists('omnyfy_queue')) {
                $setup->getConnection()->dropTable('omnyfy_queue');
            }
        }

        if (version_compare($version, '1.0.2', '<')) {
            $conn = $setup->getConnection();

            $table = $conn->getTableName('omnyfy_core_queue');
            if ($setup->tableExists($table) && $conn->tableColumnExists($table, 'message')) {
                $conn->modifyColumn($table, 'message', [
                    'type' => Table::TYPE_TEXT,
                    'length' => 2048,
                    'comment' => 'Message'
                ]);
            }
        }

        $setup->endSetup();
    }
}