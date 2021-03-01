<?php
/**
 * Project: Omnyfy Multi Vendor.
 * User: jing
 * Date: 4/4/17
 * Time: 10:55 AM
 */
namespace Omnyfy\Vendor\Setup;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;


class InstallSchema implements InstallSchemaInterface
{

    protected $roleFactory;

    protected $rulesFactory;

    protected $omnyfyHelper;

    public function __construct(
        \Magento\Authorization\Model\RoleFactory $roleFactory,
        \Magento\Authorization\Model\RulesFactory $rulesFactory,
        \Omnyfy\Vendor\Helper\Data $omnyfyHelper
    )
    {
        $this->roleFactory = $roleFactory;
        $this->rulesFactory = $rulesFactory;
        $this->omnyfyHelper = $omnyfyHelper;
    }

    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {

        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('omnyfy_vendor_vendor_entity')) {
            $vendorTable = $installer->getConnection()->newTable(
                $installer->getTable('omnyfy_vendor_vendor_entity')
            )
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                    'Entity ID'
                )
                ->addColumn(
                    'name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Business Name'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Status'
                )
                ->addColumn(
                    'address',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Business Address'
                )
                ->addColumn(
                    'phone',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Business Contact Number'
                )
                ->addColumn(
                    'email',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Business Email Address'
                )
                ->addColumn(
                    'fax',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Business Fax Number'
                )
                ->addColumn(
                    'social_media',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Business Social Media'
                )
                ->addColumn(
                    'description',
                    Table::TYPE_TEXT,
                    1024,
                    ['nullable' => true],
                    'Business Description'
                )
                ->addColumn(
                    'abn',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'ABN'
                )
                ->addColumn(
                    'logo',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Business Logo'
                )
                ->addColumn(
                    'banner',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Business Background Banner'
                )
                ->addColumn(
                    'shipping_policy',
                    Table::TYPE_TEXT,
                    1024,
                    ['nullable' => true],
                    'Shpping Policy'
                )
                ->addColumn(
                    'return_policy',
                    Table::TYPE_TEXT,
                    1024,
                    ['nullable' => true],
                    'Return Policy'
                )
                ->addColumn(
                    'payment_policy',
                    Table::TYPE_TEXT,
                    1024,
                    ['nullable' => true],
                    'Payment Policy'
                )
                ->addColumn(
                    'marketing_policy',
                    Table::TYPE_TEXT,
                    1024,
                    ['nullable' => true],
                    'Marketing Policy'
                )
            ;
            $installer->getConnection()->createTable($vendorTable);
        }

        if (!$installer->tableExists('omnyfy_vendor_location_entity')) {
            $locationTable = $installer->getConnection()->newTable(
                $installer->getTable('omnyfy_vendor_location_entity')
            )
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                    'Entity ID'
                )
                ->addColumn(
                    'vendor_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0, 'unsigned' => true],
                    'Vendor ID'
                )
                ->addColumn(
                    'priority',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 9999],
                    'Priority'
                )
                ->addColumn(
                    'location_name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Location Name'
                )
                ->addColumn(
                    'description',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Location Description'
                )
                ->addColumn(
                    'address',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Location Address'
                )
                ->addColumn(
                    'suburb',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Suburb'
                )
                ->addColumn(
                    'region',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Region'
                )
                ->addColumn(
                    'country',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Country'
                )
                ->addColumn(
                    'postcode',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'postcode'
                )
                ->addColumn(
                    'latitude',
                    Table::TYPE_DECIMAL,
                    '10,6',
                    ['nullable' => false],
                    'Latitude'
                )
                ->addColumn(
                    'longitude',
                    Table::TYPE_DECIMAL,
                    '10,6',
                    ['nullable' => false],
                    'Longitude'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    1,
                    ['nullable' => false, 'unsigned' => true, 'default' => 1],
                    'Status'
                )
                ->addForeignKey(
                    $installer->getFkName('omnyfy_vendor_location_entity', 'vendor_id', 'omnyfy_vendor_vendor_entity', 'entity_id'),
                    'vendor_id',
                    $installer->getTable('omnyfy_vendor_vendor_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
            ;
            $installer->getConnection()->createTable($locationTable);
/*
            $installer->getConnection()
                ->addForeignKey(
                    $installer->getFkName('omnyfy_location', 'vendor_id', 'omnyfy_vendor_location_entity', 'vendor_id'),
                    $installer->getTable('omnyfy_location'),
                    'vendor_id',
                    $installer->getTable('omnyfy_vendor_location_entity'),
                    'vendor_id',
                    Table::ACTION_CASCADE
                );
*/
        }


        $quoteItemTable = $installer->getTable('quote_item');
        if (!($installer->getConnection()->tableColumnExists($quoteItemTable, 'location_id')))
        {
            $installer->getConnection()
                ->addColumn(
                    $quoteItemTable,
                    'location_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Location ID',
                        'unsigned' => true,
                    ]
                )
            ;
            $installer->getConnection()
                ->addForeignKey(
                    $installer->getFkName('quote_item', 'location_id', 'omnyfy_vendor_location_entity', 'entity_id'),
                    $quoteItemTable,
                    'location_id',
                    $installer->getTable('omnyfy_vendor_location_entity'),
                    'entity_id',
                    Table::ACTION_SET_NULL
                )
            ;
        }
        if (!($installer->getConnection()->tableColumnExists($quoteItemTable, 'vendor_id')))
        {
            $installer->getConnection()
                ->addColumn(
                    $quoteItemTable,
                    'vendor_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Vendor ID',
                        'unsigned' => true,
                    ]
                )
            ;
            $installer->getConnection()
                ->addForeignKey(
                    $installer->getFkName('quote_item', 'vendor_id', 'omnyfy_vendor_vendor_entity', 'entity_id'),
                    $quoteItemTable,
                    'vendor_id',
                    $installer->getTable('omnyfy_vendor_vendor_entity'),
                    'entity_id',
                    Table::ACTION_SET_NULL
                )
            ;
        }

        $quoteShippingRateTable = $installer->getTable('quote_shipping_rate');
        if (!($installer->getConnection()->tableColumnExists($quoteShippingRateTable, 'location_id')))
        {
            $installer->getConnection()
                ->addColumn(
                    $quoteShippingRateTable,
                    'location_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'Location ID',
                        'unsigned' => true,
                        'default' => '0'
                    ]
                )
            ;
            $installer->getConnection()
                ->addForeignKey(
                    $installer->getFkName('quote_shipping_rate', 'location_id', 'omnyfy_vendor_location_entity', 'entity_id'),
                    $quoteShippingRateTable,
                    'location_id',
                    $installer->getTable('omnyfy_vendor_location_entity'),
                    'entity_id',
                    Table::ACTION_NO_ACTION
                )
            ;
        }
        if (!($installer->getConnection()->tableColumnExists($quoteShippingRateTable, 'vendor_id')))
        {
            $installer->getConnection()
                ->addColumn(
                    $quoteShippingRateTable,
                    'vendor_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'Vendor ID',
                        'unsigned' => true,
                        'default' => '0'
                    ]
                )
            ;
            $installer->getConnection()
                ->addForeignKey(
                    $installer->getFkName('quote_shipping_rate', 'vendor_id', 'omnyfy_vendor_vendor_entity', 'entity_id'),
                    $quoteShippingRateTable,
                    'vendor_id',
                    $installer->getTable('omnyfy_vendor_vendor_entity'),
                    'entity_id',
                    Table::ACTION_NO_ACTION
                )
            ;
        }

        $shipmentTable = $installer->getTable('sales_shipment');
        if (!($installer->getConnection()->tableColumnExists($shipmentTable, 'location_id')))
        {
            $installer->getConnection()
                ->addColumn(
                    $shipmentTable,
                    'location_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'Location ID',
                        'unsigned' => true,
                        'default' => '0'
                    ]
                )
            ;
            $installer->getConnection()
                ->addForeignKey(
                    $installer->getFkName('sales_shipment', 'location_id', 'omnyfy_vendor_location_entity', 'entity_id'),
                    $shipmentTable,
                    'location_id',
                    $installer->getTable('omnyfy_vendor_location_entity'),
                    'entity_id',
                    Table::ACTION_SET_DEFAULT
                )
            ;
        }
        if (!($installer->getConnection()->tableColumnExists($shipmentTable, 'vendor_id')))
        {
            $installer->getConnection()
                ->addColumn(
                    $shipmentTable,
                    'vendor_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'Vendor ID',
                        'unsigned' => true,
                        'default' => '0'
                    ]
                )
            ;
            $installer->getConnection()
                ->addForeignKey(
                    $installer->getFkName('sales_shipment', 'vendor_id', 'omnyfy_vendor_vendor_entity', 'entity_id'),
                    $shipmentTable,
                    'vendor_id',
                    $installer->getTable('omnyfy_vendor_vendor_entity'),
                    'entity_id',
                    Table::ACTION_SET_DEFAULT
                )
            ;
        }

        $shipmentGridTable = $installer->getTable('sales_shipment_grid');
        if (!($installer->getConnection()->tableColumnExists($shipmentGridTable, 'location_id')))
        {
            $installer->getConnection()
                ->addColumn(
                    $shipmentGridTable,
                    'location_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'Location ID',
                        'unsigned' => true,
                        'default' => '0',
                        'after' => 'order_id'
                    ]
                )
            ;
            $installer->getConnection()
                ->addForeignKey(
                    $installer->getFkName('sales_shipment_grid', 'location_id', 'omnyfy_vendor_location_entity', 'entity_id'),
                    $shipmentGridTable,
                    'location_id',
                    $installer->getTable('omnyfy_vendor_location_entity'),
                    'entity_id',
                    Table::ACTION_SET_DEFAULT
                )
            ;
        }
        if (!($installer->getConnection()->tableColumnExists($shipmentGridTable, 'vendor_id')))
        {
            $installer->getConnection()
                ->addColumn(
                    $shipmentGridTable,
                    'vendor_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'Vendor ID',
                        'unsigned' => true,
                        'default' => '0',
                        'after' => 'location_id'
                    ]
                )
            ;
            $installer->getConnection()
                ->addForeignKey(
                    $installer->getFkName('sales_shipment_grid', 'vendor_id', 'omnyfy_vendor_vendor_entity', 'entity_id'),
                    $shipmentGridTable,
                    'vendor_id',
                    $installer->getTable('omnyfy_vendor_vendor_entity'),
                    'entity_id',
                    Table::ACTION_SET_DEFAULT
                )
            ;
        }

        $orderItemTable = $installer->getTable('sales_order_item');
        if (!($installer->getConnection()->tableColumnExists($orderItemTable, 'location_id')))
        {
            $installer->getConnection()
                ->addColumn(
                    $orderItemTable,
                    'location_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'Location ID',
                        'unsigned' => true,
                        'default' => '0'
                    ]
                )
            ;
            $installer->getConnection()
                ->addForeignKey(
                    $installer->getFkName('sales_order_item', 'location_id', 'omnyfy_vendor_location_entity', 'entity_id'),
                    $orderItemTable,
                    'location_id',
                    $installer->getTable('omnyfy_vendor_location_entity'),
                    'entity_id',
                    Table::ACTION_SET_DEFAULT
                )
            ;
        }
        if (!($installer->getConnection()->tableColumnExists($orderItemTable, 'vendor_id')))
        {
            $installer->getConnection()
                ->addColumn(
                    $orderItemTable,
                    'vendor_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'Vendor ID',
                        'unsigned' => true,
                        'default' => '0'
                    ]
                )
            ;
            $installer->getConnection()
                ->addForeignKey(
                    $installer->getFkName('sales_order_item', 'vendor_id', 'omnyfy_vendor_vendor_entity', 'entity_id'),
                    $orderItemTable,
                    'vendor_id',
                    $installer->getTable('omnyfy_vendor_vendor_entity'),
                    'entity_id',
                    Table::ACTION_SET_DEFAULT
                )
            ;
        }

        $invoiceItemTable = $installer->getTable('sales_invoice_item');
        if (!($installer->getConnection()->tableColumnExists($invoiceItemTable, 'location_id')))
        {
            $installer->getConnection()
                ->addColumn(
                    $invoiceItemTable,
                    'location_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'Location ID',
                        'unsigned' => true,
                        'default' => '0'
                    ]
                )
            ;
            $installer->getConnection()
                ->addForeignKey(
                    $installer->getFkName('sales_invoice_item', 'location_id', 'omnyfy_vendor_location_entity', 'entity_id'),
                    $orderItemTable,
                    'location_id',
                    $installer->getTable('omnyfy_vendor_location_entity'),
                    'entity_id',
                    Table::ACTION_SET_DEFAULT
                )
            ;
        }
        if (!($installer->getConnection()->tableColumnExists($invoiceItemTable, 'vendor_id')))
        {
            $installer->getConnection()
                ->addColumn(
                    $invoiceItemTable,
                    'vendor_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'Vendor ID',
                        'unsigned' => true,
                        'default' => '0'
                    ]
                )
            ;
            $installer->getConnection()
                ->addForeignKey(
                    $installer->getFkName('sales_invoice_item', 'vendor_id', 'omnyfy_vendor_vendor_entity', 'entity_id'),
                    $orderItemTable,
                    'vendor_id',
                    $installer->getTable('omnyfy_vendor_vendor_entity'),
                    'entity_id',
                    Table::ACTION_SET_DEFAULT
                )
            ;
        }

        //invoice-vendor relation table
        if (!$installer->tableExists('omnyfy_invoice_vendor')) {
            $invoiceVendorTable = $installer->getConnection()->newTable(
                $installer->getTable('omnyfy_invoice_vendor')
            )
                ->addColumn(
                    'invoice_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Invoice ID'
                )
                ->addColumn(
                    'vendor_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Vendor ID'
                )
                ->addIndex(
                    $installer->getIdxName(
                        'omnyfy_invoice_vendor',
                        ['invoice_id', 'vendor_id'],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['invoice_id', 'vendor_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
            ;
            $installer->getConnection()->createTable($invoiceVendorTable);

            $tableName = $installer->getTable('omnyfy_invoice_vendor');
            if ($installer->tableExists($tableName)) {
                $installer->getConnection()->addForeignKey(
                    $installer->getFkName(
                        $tableName,
                        'vendor_id',
                        $installer->getTable('omnyfy_vendor_vendor_entity'),
                        'entity_id'
                    ),
                    $tableName,
                    'vendor_id',
                    $installer->getTable('omnyfy_vendor_vendor_entity'),
                    'entity_id'
                );

                $installer->getConnection()->addForeignKey(
                    $installer->getFkName(
                        $tableName,
                        'invoice_id',
                        $installer->getTable('sales_invoice'),
                        'entity_id'
                    ),
                    $tableName,
                    'invoice_id',
                    $installer->getTable('sales_invoice'),
                    'entity_id'
                );
            }
        }

        //order-vendor relation table
        if (!$installer->tableExists('omnyfy_order_vendor')) {
            $orderVendorTable = $installer->getConnection()->newTable(
                $installer->getTable('omnyfy_order_vendor')
            )
                ->addColumn(
                    'order_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Order ID'
                )
                ->addColumn(
                    'vendor_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Vendor ID'
                )
                ->addIndex(
                    $installer->getIdxName(
                        'omnyfy_order_vendor',
                        ['order_id', 'vendor_id'],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['order_id', 'vendor_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
            ;
            $installer->getConnection()->createTable($orderVendorTable);

            $tableName = $installer->getTable('omnyfy_order_vendor');
            if ($installer->tableExists($tableName)) {
                $installer->getConnection()->addForeignKey(
                    $installer->getFkName(
                        $tableName,
                        'vendor_id',
                        $installer->getTable('omnyfy_vendor_vendor_entity'),
                        'entity_id'
                    ),
                    $tableName,
                    'vendor_id',
                    $installer->getTable('omnyfy_vendor_vendor_entity'),
                    'entity_id'
                );

                $installer->getConnection()->addForeignKey(
                    $installer->getFkName(
                        $tableName,
                        'order_id',
                        $installer->getTable('sales_order'),
                        'entity_id'
                    ),
                    $tableName,
                    'order_id',
                    $installer->getTable('sales_order'),
                    'entity_id'
                );
            }
        }

        //Customer vendor
        if (!$installer->tableExists('omnyfy_customer_vendor')) {
            $customerVendorTable = $installer->getConnection()->newTable(
                $installer->getTable('omnyfy_customer_vendor')
            )
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Customer ID'
                )
                ->addColumn(
                    'vendor_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Vendor ID'
                )
                ->addIndex(
                    $installer->getIdxName(
                        'omnyfy_customer_vendor',
                        ['customer_id', 'vendor_id'],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['customer_id', 'vendor_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
            ;
            $installer->getConnection()->createTable($customerVendorTable);

            $tableName = $installer->getTable('omnyfy_customer_vendor');
            if ($installer->tableExists($tableName)) {
                $installer->getConnection()->addForeignKey(
                    $installer->getFkName(
                        $tableName,
                        'vendor_id',
                        $installer->getTable('omnyfy_vendor_vendor_entity'),
                        'entity_id'
                    ),
                    $tableName,
                    'vendor_id',
                    $installer->getTable('omnyfy_vendor_vendor_entity'),
                    'entity_id'
                );

                $installer->getConnection()->addForeignKey(
                    $installer->getFkName(
                        $tableName,
                        'customer_id',
                        $installer->getTable('customer_entity'),
                        'entity_id'
                    ),
                    $tableName,
                    'customer_id',
                    $installer->getTable('customer_entity'),
                    'entity_id'
                );
            }
        }

        //admin_user-vendor relation table
        if (!$installer->tableExists('omnyfy_admin_user_vendor')) {
            $adminUserVendorTable = $installer->getConnection()->newTable(
                $installer->getTable('omnyfy_admin_user_vendor')
            )
                ->addColumn(
                    'user_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Admin User ID'
                )
                ->addColumn(
                    'vendor_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Vendor ID'
                )
                ->addIndex(
                    $installer->getIdxName(
                        'omnyfy_admin_user_vendor',
                        ['user_id'],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['user_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ;
            $installer->getConnection()->createTable($adminUserVendorTable);
            $tableName = $installer->getTable('omnyfy_admin_user_vendor');
            if ($installer->tableExists($tableName)) {
                $installer->getConnection()->addForeignKey(
                    $installer->getFkName(
                        $tableName,
                        'vendor_id',
                        $installer->getTable('omnyfy_vendor_vendor_entity'),
                        'entity_id'
                    ),
                    $tableName,
                    'vendor_id',
                    $installer->getTable('omnyfy_vendor_vendor_entity'),
                    'entity_id'
                );

                $installer->getConnection()->addForeignKey(
                    $installer->getFkName(
                        $tableName,
                        'user_id',
                        $installer->getTable('admin_user'),
                        'user_id'
                    ),
                    $tableName,
                    'user_id',
                    $installer->getTable('admin_user'),
                    'user_id'
                );
            }
        }

        //vendor profile table, which is website to vendor relation
        if (!$installer->tableExists('omnyfy_vendor_profile')) {
            $vendorProfileTable = $installer->getConnection()->newTable(
                $installer->getTable('omnyfy_vendor_profile')
            )
                ->addColumn(
                    'profile_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'unsigned' => true, 'primary' => true],
                    'Profile ID'
                )
                ->addColumn(
                    'website_id',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Website ID'
                )
                ->addColumn(
                    'vendor_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Vendor ID'
                )
                ->addColumn(
                    'updates',
                    Table::TYPE_TEXT,
                    '65535',
                    ['nullable' => false, 'default' => ''],
                    'Updates JSON'
                )
                ->addIndex(
                    $installer->getIdxName(
                        'omnyfy_vendor_profile',
                        ['website_id', 'vendor_id'],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['website_id', 'vendor_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->addForeignKey(
                    $installer->getFkName('omnyfy_vendor_profile', 'website_id', 'store_website', 'website_id'),
                    'website_id',
                    $installer->getTable('store_website'),
                    'website_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName('omnyfy_vendor_profile', 'vendor_id', 'omnyfy_vendor_vendor_entity', 'entity_id'),
                    'vendor_id',
                    $installer->getTable('omnyfy_vendor_vendor_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ;
            $installer->getConnection()->createTable($vendorProfileTable);
        }

        //profile to admin user relation
        if (!$installer->tableExists('omnyfy_vendor_profile_admin_user')) {
            $profileAdminTable = $installer->getConnection()->newTable(
                $installer->getTable('omnyfy_vendor_profile_admin_user')
            )
                ->addColumn(
                    'profile_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Profile ID'
                )
                ->addColumn(
                    'admin_user_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Admin User ID'
                )
                ->addIndex(
                    $installer->getIdxName(
                        'omnyfy_vendor_profile_admin_user',
                        ['profile_id', 'admin_user_id'],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['profile_id', 'admin_user_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->addForeignKey(
                    $installer->getFkName('omnyfy_vendor_profile_admin_user', 'profile_id', 'omnyfy_vendor_profile', 'profile_id'),
                    'profile_id',
                    $installer->getTable('omnyfy_vendor_profile'),
                    'profile_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName('omnyfy_vendor_profile_admin_user', 'admin_user_id', 'admin_user', 'user_id'),
                    'admin_user_id',
                    $installer->getTable('admin_user'),
                    'user_id',
                    Table::ACTION_CASCADE
                )
            ;
            $installer->getConnection()->createTable($profileAdminTable);
        }

        //profile to location relation
        if (!$installer->tableExists('omnyfy_vendor_profile_location')) {
            $profileLocationTable = $installer->getConnection()->newTable(
                $installer->getTable('omnyfy_vendor_profile_location')
            )
                ->addColumn(
                    'profile_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Profile ID'
                )
                ->addColumn(
                    'location_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Location ID'
                )
                ->addIndex(
                    $installer->getIdxName(
                        'omnyfy_vendor_profile_location',
                        ['profile_id', 'location_id'],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['profile_id', 'location_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->addForeignKey(
                    $installer->getFkName('omnyfy_vendor_profile_location', 'profile_id', 'omnyfy_vendor_profile', 'profile_id'),
                    'profile_id',
                    $installer->getTable('omnyfy_vendor_profile'),
                    'profile_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName('omnyfy_vendor_profile_location', 'location_id', 'omnyfy_vendor_location_entity', 'entity_id'),
                    'location_id',
                    $installer->getTable('omnyfy_vendor_location_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ;
            $installer->getConnection()->createTable($profileLocationTable);
        }

        //product-vendor relation table
        if (!$installer->tableExists('omnyfy_product_vendor')) {
            $productVendorTable = $installer->getConnection()->newTable(
                $installer->getTable('omnyfy_product_vendor')
            )
                ->addColumn(
                    'product_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Product ID'
                )
                ->addColumn(
                    'vendor_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Vendor ID'
                )
                ->addIndex(
                    $installer->getIdxName(
                        'omnyfy_product_vendor',
                        ['product_id', 'vendor_id'],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['product_id', 'vendor_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
            ;
            $installer->getConnection()->createTable($productVendorTable);
            $tableName = $installer->getTable('omnyfy_product_vendor');
            if ($installer->tableExists($tableName)) {
                $installer->getConnection()->addForeignKey(
                    $installer->getFkName(
                        $tableName,
                        'vendor_id',
                        $installer->getTable('omnyfy_vendor_vendor_entity'),
                        'entity_id'
                    ),
                    $tableName,
                    'vendor_id',
                    $installer->getTable('omnyfy_vendor_vendor_entity'),
                    'entity_id'
                );

                $installer->getConnection()->addForeignKey(
                    $installer->getFkName(
                        $tableName,
                        'product_id',
                        $installer->getTable('catalog_product_entity'),
                        'entity_id'
                    ),
                    $tableName,
                    'product_id',
                    $installer->getTable('catalog_product_entity'),
                    'entity_id'
                );
            }
        }

        //omnyfy inventory
        if (!$installer->tableExists('omnyfy_inventory')) {
            $omnyfyInventoryTable = $installer->getConnection()->newTable(
                $installer->getTable('omnyfy_inventory')
            )
                ->addColumn(
                    'product_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Product ID'
                )
                ->addColumn(
                    'location_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Location ID'
                )
                ->addColumn(
                    'qty',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false,'default' => '0.0'],
                    'Quantity'
                )
                ->addIndex(
                    $installer->getIdxName(
                        'omnyfy_inventory',
                        ['product_id', 'location_id'],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['product_id', 'location_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
            ;
            $installer->getConnection()->createTable($omnyfyInventoryTable);
            $tableName = $installer->getTable('omnyfy_inventory');
            if ($installer->tableExists($tableName)) {
                /*
                $installer->getConnection()->addForeignKey(
                    $installer->getFkName(
                        $tableName,
                        'product_id',
                        $installer->getTable('catalog_product_entity'),
                        'entity_id'
                    ),
                    $tableName,
                    'product_id',
                    $installer->getTable('omnyfy_vendor_vendor_entity'),
                    'entity_id'
                );
                */
                $installer->getConnection()->addForeignKey(
                    $installer->getFkName(
                        $tableName,
                        'location_id',
                        $installer->getTable('omnyfy_vendor_location_entity'),
                        'entity_id'
                    ),
                    $tableName,
                    'location_id',
                    $installer->getTable('omnyfy_vendor_location_entity'),
                    'entity_id'
                );
            }
        }

        //quote_address table, increase size of shipping_method
        $quoteAddressTable = $installer->getTable('quote_address');
        $sql = sprintf('ALTER TABLE %s MODIFY COLUMN %s %s',
            $quoteAddressTable,
            $installer->getConnection()->quoteIdentifier('shipping_method'),
            'VARCHAR(255)'
            );
        $installer->getConnection()->query($sql);

        //order table, increase size of shipping_method
        $orderTable = $installer->getTable('sales_order');
        $installer->getConnection()->query(sprintf(
            'ALTER TABLE %s MODIFY COLUMN %s %s',
            $orderTable,
            $installer->getConnection()->quoteIdentifier('shipping_method'),
            'VARCHAR(255)'
        ));

        //Vendor Order Total
        if (!$installer->tableExists('omnyfy_vendor_order_total')) {
            $vendorOrderTotalTable = $installer->getConnection()->newTable(
                $installer->getTable('omnyfy_vendor_order_total')
            )
                ->addColumn(
                    'vendor_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Vendor ID'
                )
                ->addColumn(
                    'order_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Order ID'
                )
                ->addColumn(
                    'subtotal',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false,'default' => '0.0'],
                    'Total'
                )
                ->addColumn(
                    'tax_amount',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false,'default' => '0.0'],
                    'Tax'
                )
                ->addColumn(
                    'discount_amount',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false,'default' => '0.0'],
                    'Discount'
                )
                ->addColumn(
                    'shipping_amount',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false,'default' => '0.0'],
                    'Shipping Total'
                )
                ->addIndex(
                    $installer->getIdxName(
                        'omnyfy_vendor_order_total',
                        ['vendor_id', 'order_id'],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['vendor_id', 'order_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->addForeignKey(
                    $installer->getFkName('omnyfy_vendor_order_total', 'order_id', 'sales_order', 'entity_id'),
                    'order_id',
                    $installer->getTable('sales_order'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName('omnyfy_vendor_order_total', 'vendor_id', 'omnyfy_vendor_vendor_entity', 'entity_id'),
                    'vendor_id',
                    $installer->getTable('omnyfy_vendor_vendor_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
            ;
            $installer->getConnection()->createTable($vendorOrderTotalTable);
        }

        //Vendor Invoice Total
        if (!$installer->tableExists('omnyfy_vendor_invoice_total')) {
            $vendorInvoiceTotalTable = $installer->getConnection()->newTable(
                $installer->getTable('omnyfy_vendor_invoice_total')
            )
                ->addColumn(
                    'vendor_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Vendor ID'
                )
                ->addColumn(
                    'invoice_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Invoice ID'
                )
                ->addColumn(
                    'subtotal',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false,'default' => '0.0'],
                    'Subtotal'
                )
                ->addColumn(
                    'tax_amount',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false,'default' => '0.0'],
                    'Tax'
                )
                ->addColumn(
                    'discount_amount',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false,'default' => '0.0'],
                    'Discount'
                )
                ->addColumn(
                    'shipping_amount',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false,'default' => '0.0'],
                    'Shipping Total'
                )
                ->addIndex(
                    $installer->getIdxName(
                        'omnyfy_vendor_invoice_total',
                        ['vendor_id', 'invoice_id'],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['vendor_id', 'invoice_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->addForeignKey(
                    $installer->getFkName('omnyfy_vendor_invoice_total', 'invoice_id', 'sales_invoice', 'entity_id'),
                    'invoice_id',
                    $installer->getTable('sales_invoice'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName('omnyfy_vendor_invoice_total', 'vendor_id', 'omnyfy_vendor_vendor_entity', 'entity_id'),
                    'vendor_id',
                    $installer->getTable('omnyfy_vendor_vendor_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
            ;
            $installer->getConnection()->createTable($vendorInvoiceTotalTable);
        }

        //omnyfy form

        //omnyfy form field

        //omnyfy form data

        //check if vendor admin role exists, create it if not there
        $vendorAdminRoleName = 'Vendor Admin';
        if (!$this->isRoleExists($installer, $vendorAdminRoleName))
        {
            $role = $this->roleFactory->create();
            $role->setName($vendorAdminRoleName)
                ->setPid(0)
                ->setRoleType(RoleGroup::ROLE_TYPE)
                ->setUserType(UserContextInterface::USER_TYPE_ADMIN)
                ;
            $role->save();
            $resource = [
                'Magento_Backend::admin',
                'Magento_Sales::sales',
                'Magento_Sales::actions_view',
                'Magento_Sales::sales_invoice',
                'Magento_Sales::shipment',
/*
                'Omnyfy_Vendor::location',
                'Omnyfy_Vendor::profile',
                'Omnyfy_Vendor::product',
                'Omnyfy_Vendor::inventory'
*/
            ];
            $this->rulesFactory->create()
                ->setRoleId($role->getId())
                ->setResources($resource)
                ->saveRel();
        }

        $installer->endSetup();
    }

    private function isRoleExists($installer, $roleName)
    {

        $roleIds = $this->omnyfyHelper->getRoleIdsByName($roleName, $installer);
        if (!empty($roleIds)) {
            return true;
        }

        return false;
    }
}






