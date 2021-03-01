<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-08-15
 * Time: 15:10
 */
namespace Omnyfy\VendorSubscription\Setup;

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
        $conn = $setup->getConnection();

        $version = $context->getVersion();
        if (version_compare($version, '1.0.2', '<')) {
            $table = $conn->getTableName('omnyfy_vendorsubscription_usage');
            if (!$setup->tableExists($table)) {
                $usageTable = $conn->newTable($table)
                    ->addColumn(
                        'id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true],
                        'Usage ID'
                    )
                    ->addColumn(
                        'vendor_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Vendor ID'
                    )
                    ->addColumn(
                        'usage_type_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Usage Type ID'
                    )
                    ->addColumn(
                        'usage_limit',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'default' => 0 ],
                        'Usage Limit'
                    )
                    ->addColumn(
                        'usage_count',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'default' => 0 ],
                        'Usage Count'
                    )
                    ->addColumn(
                        'start_date',
                        Table::TYPE_DATETIME,
                        null,
                        ['nullable' => false],
                        'Start Date'
                    )
                    ->addColumn(
                        'end_date',
                        Table::TYPE_DATETIME,
                        null,
                        ['nullable' => true],
                        'End Date'
                    )
                    ->addIndex(
                        $setup->getIdxName($table, ['vendor_id', 'usage_type_id'], AdapterInterface::INDEX_TYPE_INDEX),
                        ['vendor_id', 'usage_type_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_INDEX]
                    )
                    ->addForeignKey(
                        $setup->getFkName(
                            'omnyfy_vendorsubscription_usage',
                            'vendor_id',
                            'omnyfy_vendor_vendor_entity',
                            'entity_id'
                        ),
                        'vendor_id',
                        $setup->getTable('omnyfy_vendor_vendor_entity'),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                ;

                $conn->createTable($usageTable);
            }

            $table = $conn->getTableName('omnyfy_vendorsubscription_usage_log');
            if (!$setup->tableExists($table)) {
                $logTable = $conn->newTable($table)
                    ->addColumn(
                        'vendor_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Vendor ID'
                    )
                    ->addColumn(
                        'usage_type_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Usage Type ID'
                    )
                    ->addColumn(
                        'object_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Object ID'
                    )
                    ->addColumn(
                        'is_deleted',
                        Table::TYPE_SMALLINT,
                        null,
                        ['nullable' => false, 'unsigned' => true, 'default' => 0],
                        'Is Deleted'
                    )
                    ->addColumn(
                        'created_at',
                        Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                        'Created At'
                    )
                    ->addColumn(
                        'updated_at',
                        Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                        'Updated At'
                    )
                    ->addIndex(
                        $setup->getIdxName($table, ['vendor_id', 'usage_type_id'], AdapterInterface::INDEX_TYPE_INDEX),
                        ['vendor_id', 'usage_type_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_INDEX]
                    )
                    ->addIndex(
                        $setup->getIdxName($table, ['vendor_id', 'usage_type_id', 'object_id'], AdapterInterface::INDEX_TYPE_UNIQUE),
                        ['vendor_id', 'usage_type_id', 'object_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    )
                ;

                $conn->createTable($logTable);
            }
        }

        if (version_compare($version, '1.0.3', '<')) {
            $table = $conn->getTableName('omnyfy_vendorsubscription_update');
            if (!$setup->tableExists($table)) {
                $updateTable = $conn->newTable($table)
                    ->addColumn(
                        'update_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                        'Update ID'
                    )
                    ->addColumn(
                        'vendor_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Vendor ID'
                    )
                    ->addColumn(
                        'vendor_type_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Vendor Type ID'
                    )
                    ->addColumn(
                        'subscription_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Subscription ID'
                    )
                    ->addColumn(
                        'from_plan_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Usage Type ID'
                    )
                    ->addColumn(
                        'from_plan_name',
                        Table::TYPE_TEXT,
                        255,
                        ['nullable' => true],
                        'From Plan Name'
                    )
                    ->addColumn(
                        'to_plan_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Object ID'
                    )
                    ->addColumn(
                        'to_plan_name',
                        Table::TYPE_TEXT,
                        255,
                        ['nullable' => true],
                        'To Plan Name'
                    )

                    ->addColumn(
                        'status',
                        Table::TYPE_SMALLINT,
                        null,
                        ['nullable' => false, 'unsigned' => true]
                    )
                    ->addColumn(
                        'created_at',
                        Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                        'Created At'
                    )
                    ->addColumn(
                        'updated_at',
                        Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                        'Updated At'
                    )
                    ->addIndex(
                        $setup->getIdxName($table, ['vendor_id'], AdapterInterface::INDEX_TYPE_INDEX),
                        ['vendor_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_INDEX]
                    )
                ;

                $conn->createTable($updateTable);
            }
        }

        if (version_compare($version, '1.0.4', '<')) {
            $table = $conn->getTableName('omnyfy_vendorsubscription_package');
            if (!$setup->tableExists($table)) {
                $leadsPackageTable = $conn->newTable($table)
                    ->addColumn(
                        'package_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                        'Package ID'
                    )
                    ->addColumn(
                        'name',
                        Table::TYPE_TEXT,
                        255,
                        ['nullable' => false],
                        'Package Name'
                    )
                    ->addColumn(
                        'price',
                        Table::TYPE_DECIMAL,
                        '12,4',
                        ['nullable' => false],
                        'Price'
                    )
                    ->addColumn(
                        'gateway_id',
                        Table::TYPE_TEXT,
                        255,
                        ['nullable' => false],
                        'Gateway ID'
                    )
                    ->addColumn(
                        'created_at',
                        Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                        'Created At'
                    )
                    ->addColumn(
                        'updated_at',
                        Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                        'Updated At'
                    )
                    ;

                $conn->createTable($leadsPackageTable);
            }

            $table = $conn->getTableName('omnyfy_vendorsubscription_package_usage');
            if (!$setup->tableExists($table)) {
                $packageUsageTable = $conn->newTable($table)
                    ->addColumn(
                        'id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                        'ID'
                    )
                    ->addColumn(
                        'package_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Package ID'
                    )
                    ->addColumn(
                        'usage_type_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Usage Type ID'
                    )
                    ->addColumn(
                        'usage_limit',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Usage Limit'
                    )
                    ->addColumn(
                        'interval',
                        Table::TYPE_SMALLINT,
                        null,
                        ['nullable' => false],
                        'Interval'
                    )
                    ->addIndex(
                        $setup->getIdxName(
                            $table,
                            ['package_id', 'usage_type_id'],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        ['package_id', 'usage_type_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    )
                    ->addForeignKey(
                        $setup->getFkName(
                            $table,
                            'package_id',
                            'omnyfy_vendorsubscription_package',
                            'package_id'
                        ),
                        'package_id',
                        'omnyfy_vendorsubscription_package',
                        'package_id',
                        Table::ACTION_CASCADE
                    )
                    ;
                $conn->createTable($packageUsageTable);
            }

            $table = $conn->getTableName('omnyfy_vendorsubscription_plan_usage');
            if (!$setup->tableExists($table)) {
                $planUsageTable = $conn->newTable($table)
                    ->addColumn(
                        'id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                        'ID'
                    )
                    ->addColumn(
                        'plan_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Plan ID'
                    )
                    ->addColumn(
                        'usage_type_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Usage Type ID'
                    )
                    ->addColumn(
                        'usage_limit',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Usage Limit'
                    )
                    ->addIndex(
                        $setup->getIdxName(
                            $table,
                            ['plan_id', 'usage_type_id'],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        ['plan_id', 'usage_type_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    )
                    ->addForeignKey(
                        $setup->getFkName(
                            $table,
                            'plan_id',
                            'omnyfy_vendorsubscription_plan',
                            'plan_id'
                        ),
                        'plan_id',
                        'omnyfy_vendorsubscription_plan',
                        'plan_id',
                        Table::ACTION_CASCADE
                    )
                    ;
                $conn->createTable($planUsageTable);
            }

            $table = $conn->getTableName('omnyfy_vendorsubscription_topup');
            if (!$setup->tableExists($table)) {
                $topupTable = $conn->newTable($table)
                    ->addColumn(
                        'topup_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                        'Top Up ID'
                    )
                    ->addColumn(
                        'vendor_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Vendor ID'
                    )
                    ->addColumn(
                        'vendor_name',
                        Table::TYPE_TEXT,
                        255,
                        ['nullable' => false],
                        'Vendor Name'
                    )
                    ->addColumn(
                        'package_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Package ID'
                    )
                    ->addColumn(
                        'package_gateway_id',
                        Table::TYPE_TEXT,
                        255,
                        ['nullable' => false],
                        'Package Gateway ID'
                    )
                    ->addColumn(
                        'price',
                        Table::TYPE_DECIMAL,
                        '12,4',
                        ['nullable' => false],
                        'Price'
                    )
                    ->addColumn(
                        'gateway_id',
                        Table::TYPE_TEXT,
                        255,
                        ['nullable' => false],
                        'Gateway ID'
                    )
                    ->addColumn(
                        'status',
                        Table::TYPE_SMALLINT,
                        null,
                        ['nullable' => false],
                        'Status'
                    )
                    ->addColumn(
                        'created_at',
                        Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                        'Created At'
                    )
                    ->addColumn(
                        'updated_at',
                        Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                        'Updated At'
                    )
                    ;

                $conn->createTable($topupTable);
            }

            $table = $conn->getTableName('omnyfy_vendorsubscription_history');
            if ($setup->tableExists($table)) {
                if (!$conn->tableColumnExists($table, 'end_date')) {
                    $conn->addColumn(
                        $table,
                        'end_date',
                        [
                            'type' => Table::TYPE_DATETIME,
                            'nullable' => true,
                            'default' => null,
                            'comment' => 'End Date',
                            'after' => 'billing_date'
                        ]
                    );
                }
                if (!$conn->tableColumnExists($table, 'start_date')) {
                    $conn->addColumn(
                        $table,
                        'start_date',
                        [
                            'type' => Table::TYPE_DATETIME,
                            'nullable' => true,
                            'default' => null,
                            'comment' => 'Start Date',
                            'after' => 'billing_date'
                        ]
                    );
                }

            }

            $table = $conn->getTableName('omnyfy_vendorsubscription_usage');
            if ($setup->tableExists($table)) {
                if (!$conn->tableColumnExists($table, 'is_one_off')) {
                    $conn->addColumn(
                        $table,
                        'is_one_off',
                        [
                            'type' => Table::TYPE_SMALLINT,
                            'nullable' => false,
                            'default' => 0,
                            'comment' => 'Is Oneoff Flag',
                            'after' => 'usage_type_id'
                        ]
                    );
                }
                if (!$conn->tableColumnExists($table, 'plan_id')) {
                    $conn->addColumn(
                        $table,
                        'plan_id',
                        [
                            'type' => Table::TYPE_INTEGER,
                            'nullable' => true,
                            'default' => null,
                            'comment' => 'Plan ID',
                            'after' => 'usage_type_id'
                        ]
                    );
                }
                if (!$conn->tableColumnExists($table, 'package_id')) {
                    $conn->addColumn(
                        $table,
                        'package_id',
                        [
                            'type' => Table::TYPE_INTEGER,
                            'nullable' => true,
                            'default' => null,
                            'comment' => 'Package ID',
                            'after' => 'usage_type_id'
                        ]
                    );
                }
            }

            $table = $conn->getTableName('omnyfy_vendorsubscription_usage_log');
            if ($setup->tableExists($table)) {
                if (!$conn->tableColumnExists($table, 'is_deleted')) {
                    $conn->addColumn(
                        $table,
                        'is_deleted',
                        [
                            'type' => Table::TYPE_SMALLINT,
                            'nullable' => false,
                            'unsigned' => true,
                            'default' => 0,
                            'comment' => 'Is Deleted',
                            'after' => 'object_id'
                        ]
                    );
                }
            }

            $table = $conn->getTableName('omnyfy_vendorsubscription_plan');
            if ($setup->tableExists($table)) {
                if (!$conn->tableColumnExists($table, 'enquiry_limit')) {
                    $conn->addColumn(
                        $table,
                        'enquiry_limit',
                        [
                            'type' => Table::TYPE_INTEGER,
                            'nullable' => false,
                            'unsigned' => true,
                            'default' => 0,
                            'comment' => 'Enquiry Limit',
                            'after' => 'kit_store_limit'
                        ]
                    );
                }

                if (!$conn->tableColumnExists($table, 'request_for_quote_limit')) {
                    $conn->addColumn(
                        $table,
                        'request_for_quote_limit',
                        [
                            'type' => Table::TYPE_INTEGER,
                            'nullable' => false,
                            'unsigned' => true,
                            'default' => 0,
                            'comment' => 'Enquiry Limit',
                            'after' => 'kit_store_limit'
                        ]
                    );
                }
            }
        }

        if (version_compare($version, '1.0.5', '<')) {
            $table = $conn->getTableName('omnyfy_vendorsubscription_plan');
            if ($setup->tableExists($table)) {
                if (!$conn->tableColumnExists($table, 'trial_days')) {
                    $conn->addColumn(
                        $table,
                        'trial_days',
                        [
                            'type' => Table::TYPE_INTEGER,
                            'nullable' => false,
                            'unsigned' => true,
                            'default' => 0,
                            'comment' => 'Trial Days',
                            'after' => 'promo_text'
                        ]
                    );
                }
            }

            $subTable = $conn->getTableName('omnyfy_vendorsubscription_subscription');
            if ($setup->tableExists($subTable)) {
                if (!$conn->tableColumnExists($subTable, 'trial_days')) {
                    $conn->addColumn(
                        $subTable,
                        'trial_days',
                        [
                            'type' => Table::TYPE_INTEGER,
                            'nullable' => false,
                            'unsigned' => true,
                            'default' => 0,
                            'comment' => 'Trial Days',
                            'after' => 'billing_interval'
                        ]
                    );
                }
            }
        }

        $setup->endSetup();
    }
}
 
