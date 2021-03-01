<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-06-28
 * Time: 10:44
 */
namespace Omnyfy\VendorSubscription\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $conn = $setup->getConnection();

        $tableName = $conn->getTableName('omnyfy_vendorsubscription_plan');
        if (!$setup->tableExists($tableName)) {
            $planTable = $conn->newTable($tableName)
                ->addColumn(
                    'plan_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                    'Plan ID'
                )
                ->addColumn(
                    'plan_name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Plan Name'
                )
                ->addColumn(
                    'is_free',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Is Free'
                )
                ->addColumn(
                    'price',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false],
                    'Plan Price'
                )
                ->addColumn(
                    'interval',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Interval'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Status'
                )
                ->addColumn(
                    'gateway_id',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Gaeway ID'
                )
                ->addColumn(
                    'show_on_front',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '0', 'unsigned' => true],
                    'Show on Front end'
                )
                ->addColumn(
                    'description',
                    Table::TYPE_TEXT,
                    1024,
                    ['nullable' => true],
                    'Description'
                )
                ->addColumn(
                    'benefits',
                    Table::TYPE_TEXT,
                    1024,
                    ['nullable' => true],
                    'Benefits'
                )
                ->addColumn(
                    'button_label',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Button Label'
                )
                ->addColumn(
                    'promo_text',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Promo Text'
                )
                ->addColumn(
                    'product_limit',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Product Limit'
                )
                ->addColumn(
                    'kit_store_limit',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Kit Store Limit'
                )
                ->addIndex(
                    $setup->getIdxName(
                        $tableName,
                        ['gateway_id'],
                        AdapterInterface::INDEX_TYPE_INDEX
                    ),
                    ['gateway_id'],
                    AdapterInterface::INDEX_TYPE_INDEX
                )
            ;
            $conn->createTable($planTable);
        }

        $tableName = $conn->getTableName('omnyfy_vendorsubscription_subscription');
        if (!$setup->tableExists($tableName)) {
            $planTable = $conn->newTable($tableName)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                    'Subscription ID'
                )
                ->addColumn(
                    'vendor_id',
                    Table::TYPE_INTEGER,
                    null,
                    [' nullable' => false, 'unsigned' => true],
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
                    'vendor_email',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Vendor Email'
                )
                ->addColumn(
                    'plan_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Plan ID'
                )
                ->addColumn(
                    'plan_name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Plan Name'
                )
                ->addColumn(
                    'plan_price',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false],
                    'Plan Price'
                )
                ->addColumn(
                    'billing_interval',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Billing Interval'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Status'
                )
                ->addColumn(
                    'plan_gateway_id',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Plan Gateway ID'
                )
                ->addColumn(
                    'gateway_id',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Subscription Gateway ID'
                )
                ->addColumn(
                    'customer_gateway_id',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Customer Gateway ID'
                )
                ->addColumn(
                    'vendor_type_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Vendor Type ID'
                )
                ->addColumn(
                    'role_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'User Group Role ID'
                )
                ->addColumn(
                    'show_on_front',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '0', 'unsigned' => true],
                    'Show on Front end'
                )
                ->addColumn(
                    'next_billing_at',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => true],
                    'Next Billing Date'
                )
                ->addColumn(
                    'cancelled_at',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => true],
                    'Cancelled Datetime'
                )
                ->addColumn(
                    'expiry_at',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => true],
                    'Expiry Datetime'
                )
                ->addColumn(
                    'description',
                    Table::TYPE_TEXT,
                    1024,
                    ['nullable' => true],
                    'Description'
                )
                ->addColumn(
                    'extra_info',
                    Table::TYPE_TEXT,
                    1024,
                    ['nullable' => true],
                    'Extra Information'
                )
                ->addIndex(
                    $setup->getIdxName(
                        $tableName,
                        ['vendor_id'],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['vendor_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->addForeignKey(
                    $setup->getFkName(
                        $tableName,
                        'vendor_type_id',
                        'omnyfy_vendor_vendor_type',
                        'type_id'
                    ),
                    'vendor_type_id',
                    $setup->getTable('omnyfy_vendor_vendor_type'),
                    'type_id',
                    Table::ACTION_CASCADE
                )
            ;
            $conn->createTable($planTable);
        }

        $tableName = $conn->getTableName('omnyfy_vendorsubscription_history');
        if (!$setup->tableExists($tableName)) {
            $historyTable = $conn->newTable($tableName)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                    'History ID'
                )
                ->addColumn(
                    'plan_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Plan ID'
                )
                ->addColumn(
                    'plan_name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Plan Name'
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
                    'subscription_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Subscription ID'
                )
                ->addColumn(
                    'sub_gateway_id',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Subscription Gateway ID'
                )
                ->addColumn(
                    'customer_gateway_id',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Customer Gateway ID'
                )
                ->addColumn(
                    'invoice_gateway_id',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Invoice Gateway ID'
                )
                ->addColumn(
                    'billing_date',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'Billing Date'
                )
                ->addColumn(
                    'billing_account_name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => ''],
                    'Billing Account Name'
                )
                ->addColumn(
                    'plan_price',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false],
                    'Plan Price'
                )
                ->addColumn(
                    'billing_amount',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false],
                    'Billing Amount'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Status'
                )
                ->addColumn(
                    'invoice_link',
                    Table::TYPE_TEXT,
                    1024,
                    ['nullable' => true],
                    'Invoice Link'
                )
            ;
            
            $conn->createTable($historyTable);
        }

        $tableName = $conn->getTableName('omnyfy_vendorsubscription_vendor_type_plan');
        if (!$setup->tableExists($tableName)) {
            $typePlanTable = $conn->newTable($tableName)
                ->addColumn(
                    'type_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Vendor Type ID'
                )
                ->addColumn(
                    'plan_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Plan ID'
                )
                ->addColumn(
                    'role_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true, 'unsigned' => true],
                    'User Role ID'
                )
                ->addColumn(
                    'config',
                    Table::TYPE_TEXT,
                    1024,
                    ['nullable' => true, 'default' => null],
                    'Configuration'
                )
                ->addIndex(
                    $setup->getIdxName(
                        $tableName,
                        ['type_id', 'plan_id'],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['type_id', 'plan_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->addIndex(
                    $setup->getIdxName(
                        $tableName,
                        ['type_id'],
                        AdapterInterface::INDEX_TYPE_INDEX
                    ),
                    ['type_id'],
                    AdapterInterface::INDEX_TYPE_INDEX
                )
                ->addForeignKey(
                    $setup->getFkName(
                        $tableName,
                        'type_id',
                        'omnyfy_vendor_vendor_type',
                        'type_id'
                    ),
                    'type_id',
                    $setup->getTable('omnyfy_vendor_vendor_type'),
                    'type_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $setup->getFkName(
                        $tableName,
                        'plan_id',
                        'omnyfy_vendorsubscription_plan',
                        'plan_id'
                    ),
                    'plan_id',
                    $setup->getTable('omnyfy_vendorsubscription_plan'),
                    'plan_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $setup->getFkName(
                        $tableName,
                        'role_id',
                        'authorization_role',
                        'role_id'
                    ),
                    'role_id',
                    $setup->getTable('authorization_role'),
                    'role_id',
                    Table::ACTION_CASCADE
                )
            ;
            $conn->createTable($typePlanTable);
        }

        $setup->endSetup();
    }
}
 