<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 4/9/17
 * Time: 10:45 AM
 */

namespace Omnyfy\Vendor\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Omnyfy\Vendor\Model\Resource\Vendor\Gallery;

class UpgradeSchema implements UpgradeSchemaInterface
{
    const GALLERY_VALUE_VIDEO_TABLE = 'omnyfy_vendor_entity_media_gallery_value_video';

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $version = $context->getVersion();
        if (version_compare($version, '1.0.1', '<')) {

            if (!$setup->tableExists('omnyfy_vendor_eav_attribute')) {
                $locationEavAttributeTable = $setup->getConnection()->newTable(
                    $setup->getTable('omnyfy_vendor_eav_attribute')
                )
                    ->addColumn(
                        'attribute_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['nullable' => false, 'primary' => true, 'unsigned' => true],
                        'Attribute ID'
                    )
                    ->addColumn(
                        'is_visible',
                        Table::TYPE_SMALLINT,
                        5,
                        ['nullable' => false, 'unsigned' => true, 'default' => 1],
                        'Is Visible'
                    )
                    ->addColumn(
                        'is_searchable',
                        Table::TYPE_SMALLINT,
                        5,
                        ['nullable' => false, 'unsigned' => true, 'default' => 0],
                        'Is Searchable'
                    )
                    ->addColumn(
                        'is_filterable',
                        Table::TYPE_SMALLINT,
                        5,
                        ['nullable' => false, 'unsigned' => true, 'default' => 0],
                        'Is Filterable'
                    )
                    ->addColumn(
                        'used_in_listing',
                        Table::TYPE_SMALLINT,
                        5,
                        ['nullable' => false, 'unsigned' => true, 'default' => 0],
                        'Is Used in Listing'
                    )
                    ->addColumn(
                        'is_used_in_grid',
                        Table::TYPE_SMALLINT,
                        5,
                        ['nullable' => false, 'unsigned' => true, 'default' => 0],
                        'Is Used in Grid'
                    )
                    ->addColumn(
                        'is_visible_in_grid',
                        Table::TYPE_SMALLINT,
                        5,
                        ['nullable' => false, 'unsigned' => true, 'default' => 0],
                        'Is Visible in Grid'
                    )
                    ->addColumn(
                        'is_filterable_in_grid',
                        Table::TYPE_SMALLINT,
                        5,
                        ['nullable' => false, 'unsigned' => true, 'default' => 0],
                        'Is Filterable in Grid'
                    )
                    ->addColumn(
                        'position',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => true],
                        'Position'
                    )
                    ->addForeignKey(
                        $setup->getFkName(
                            'omnyfy_vendor_eav_attribute',
                            'attribute_id',
                            'eav_attribute',
                            'attribute_id'
                        ),
                        'attribute_id',
                        $setup->getTable('eav_attribute'),
                        'attribute_id',
                        Table::ACTION_CASCADE
                    )
                ;
                $setup->getConnection()->createTable($locationEavAttributeTable);
            }

            $locationEntity = \Omnyfy\Vendor\Model\Location::ENTITY . '_entity';
            $tableName = $locationEntity . '_int';
            if (!$setup->tableExists($tableName)) {
                $table = $setup->getConnection()->newTable(
                    $setup->getTable($tableName)
                )
                    ->addColumn(
                        'value_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true],
                        'Value ID'
                    )
                    ->addColumn(
                        'attribute_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Attribute ID'
                    )
                    ->addColumn(
                        'store_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Store ID'
                    )
                    ->addColumn(
                        'entity_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Entity ID'
                    )
                    ->addColumn(
                        'value',
                        Table::TYPE_INTEGER,
                        null,
                        [],
                        'Value'
                    )
                    ->addIndex(
                        $setup->getIdxName(
                            $tableName,
                            ['entity_id', 'attribute_id', 'store_id'],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        ['entity_id', 'attribute_id', 'store_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    )
                    ->addIndex(
                        $setup->getIdxName($tableName, ['attribute_id']),
                        ['attribute_id']
                    )
                    ->addIndex(
                        $setup->getIdxName($tableName, ['store_id']),
                        ['store_id']
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'attribute_id', 'eav_attribute', 'attribute_id'),
                        'attribute_id',
                        $setup->getTable('eav_attribute'),
                        'attribute_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'entity_id', $locationEntity, 'entity_id'),
                        'entity_id',
                        $setup->getTable($locationEntity),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'store_id', 'store', 'store_id'),
                        'store_id',
                        $setup->getTable('store'),
                        'store_id',
                        Table::ACTION_CASCADE
                    )
                    ->setComment('Omnyfy Location Integer Attribute Backend Table')
                ;
                $setup->getConnection()->createTable($table);
            }
            $tableName = $locationEntity . '_datetime';
            if (!$setup->tableExists($tableName)) {
                $table = $setup->getConnection()->newTable(
                    $setup->getTable($tableName)
                )
                    ->addColumn(
                        'value_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true],
                        'Value ID'
                    )
                    ->addColumn(
                        'attribute_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Attribute ID'
                    )
                    ->addColumn(
                        'store_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Store ID'
                    )
                    ->addColumn(
                        'entity_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Entity ID'
                    )
                    ->addColumn(
                        'value',
                        Table::TYPE_DATETIME,
                        null,
                        [],
                        'Value'
                    )
                    ->addIndex(
                        $setup->getIdxName(
                            $tableName,
                            ['entity_id', 'attribute_id', 'store_id'],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        ['entity_id', 'attribute_id', 'store_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    )
                    ->addIndex(
                        $setup->getIdxName($tableName, ['attribute_id']),
                        ['attribute_id']
                    )
                    ->addIndex(
                        $setup->getIdxName($tableName, ['store_id']),
                        ['store_id']
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'attribute_id', 'eav_attribute', 'attribute_id'),
                        'attribute_id',
                        $setup->getTable('eav_attribute'),
                        'attribute_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'entity_id', $locationEntity, 'entity_id'),
                        'entity_id',
                        $setup->getTable($locationEntity),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'store_id', 'store', 'store_id'),
                        'store_id',
                        $setup->getTable('store'),
                        'store_id',
                        Table::ACTION_CASCADE
                    )
                    ->setComment('Omnyfy Location Datetime Attribute Backend Table')
                ;
                $setup->getConnection()->createTable($table);
            }
            $tableName = $locationEntity . '_decimal';
            if (!$setup->tableExists($tableName)) {
                $table = $setup->getConnection()->newTable(
                    $setup->getTable($tableName)
                )
                    ->addColumn(
                        'value_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true],
                        'Value ID'
                    )
                    ->addColumn(
                        'attribute_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Attribute ID'
                    )
                    ->addColumn(
                        'store_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Store ID'
                    )
                    ->addColumn(
                        'entity_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Entity ID'
                    )
                    ->addColumn(
                        'value',
                        Table::TYPE_DECIMAL,
                        '12,4',
                        [],
                        'Value'
                    )
                    ->addIndex(
                        $setup->getIdxName(
                            $tableName,
                            ['entity_id', 'attribute_id', 'store_id'],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        ['entity_id', 'attribute_id', 'store_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    )
                    ->addIndex(
                        $setup->getIdxName($tableName, ['attribute_id']),
                        ['attribute_id']
                    )
                    ->addIndex(
                        $setup->getIdxName($tableName, ['store_id']),
                        ['store_id']
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'attribute_id', 'eav_attribute', 'attribute_id'),
                        'attribute_id',
                        $setup->getTable('eav_attribute'),
                        'attribute_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'entity_id', $locationEntity, 'entity_id'),
                        'entity_id',
                        $setup->getTable($locationEntity),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'store_id', 'store', 'store_id'),
                        'store_id',
                        $setup->getTable('store'),
                        'store_id',
                        Table::ACTION_CASCADE
                    )
                    ->setComment('Omnyfy Location Decimal Attribute Backend Table')
                ;
                $setup->getConnection()->createTable($table);
            }
            $tableName = $locationEntity . '_varchar';
            if (!$setup->tableExists($tableName)) {
                $table = $setup->getConnection()->newTable(
                    $setup->getTable($tableName)
                )
                    ->addColumn(
                        'value_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true],
                        'Value ID'
                    )
                    ->addColumn(
                        'attribute_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Attribute ID'
                    )
                    ->addColumn(
                        'store_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Store ID'
                    )
                    ->addColumn(
                        'entity_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Entity ID'
                    )
                    ->addColumn(
                        'value',
                        Table::TYPE_TEXT,
                        255,
                        [],
                        'Value'
                    )
                    ->addIndex(
                        $setup->getIdxName(
                            $tableName,
                            ['entity_id', 'attribute_id', 'store_id'],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        ['entity_id', 'attribute_id', 'store_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    )
                    ->addIndex(
                        $setup->getIdxName($tableName, ['attribute_id']),
                        ['attribute_id']
                    )
                    ->addIndex(
                        $setup->getIdxName($tableName, ['store_id']),
                        ['store_id']
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'attribute_id', 'eav_attribute', 'attribute_id'),
                        'attribute_id',
                        $setup->getTable('eav_attribute'),
                        'attribute_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'entity_id', $locationEntity, 'entity_id'),
                        'entity_id',
                        $setup->getTable($locationEntity),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'store_id', 'store', 'store_id'),
                        'store_id',
                        $setup->getTable('store'),
                        'store_id',
                        Table::ACTION_CASCADE
                    )
                    ->setComment('Omnyfy Location Varchar Attribute Backend Table')
                ;
                $setup->getConnection()->createTable($table);
            }
            $tableName = $locationEntity . '_text';
            if (!$setup->tableExists($tableName)) {
                $table = $setup->getConnection()->newTable(
                    $setup->getTable($tableName)
                )
                    ->addColumn(
                        'value_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true],
                        'Value ID'
                    )
                    ->addColumn(
                        'attribute_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Attribute ID'
                    )
                    ->addColumn(
                        'store_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Store ID'
                    )
                    ->addColumn(
                        'entity_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Entity ID'
                    )
                    ->addColumn(
                        'value',
                        Table::TYPE_TEXT,
                        '64k',
                        [],
                        'Value'
                    )
                    ->addIndex(
                        $setup->getIdxName(
                            $tableName,
                            ['entity_id', 'attribute_id', 'store_id'],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        ['entity_id', 'attribute_id', 'store_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    )
                    ->addIndex(
                        $setup->getIdxName($tableName, ['attribute_id']),
                        ['attribute_id']
                    )
                    ->addIndex(
                        $setup->getIdxName($tableName, ['store_id']),
                        ['store_id']
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'attribute_id', 'eav_attribute', 'attribute_id'),
                        'attribute_id',
                        $setup->getTable('eav_attribute'),
                        'attribute_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'entity_id', $locationEntity, 'entity_id'),
                        'entity_id',
                        $setup->getTable($locationEntity),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'store_id', 'store', 'store_id'),
                        'store_id',
                        $setup->getTable('store'),
                        'store_id',
                        Table::ACTION_CASCADE
                    )
                    ->setComment('Omnyfy Location Text Attribute Backend Table')
                ;
                $setup->getConnection()->createTable($table);
            }
        }

        if (version_compare($version, '1.0.2', '<')) {


            //add region_id column into location table
            $locationTable = $setup->getConnection()->getTableName('omnyfy_vendor_location_entity');
            if ($setup->tableExists($locationTable) && !$setup->getConnection()->tableColumnExists($locationTable, 'region_id')) {
                $setup->getConnection()->addColumn(
                    $locationTable,
                    'region_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'Region ID',
                        'unsigned' => true,
                        'default' => '0'
                    ]
                )
                ;
            }

            /*
            $setup->getConnection()
                ->addForeignKey(
                    $setup->getFkName(
                        'omnyfy_location',
                        'region_id',
                        'directory_country_region',
                        'region_id'
                    ),
                    $locationTable,
                    'region_id',
                    $setup->getTable('directory_country_region'),
                    'region_id',
                    Table::ACTION_SET_DEFAULT
                )
            ;
            */

            //Add eav attribute tables for vendor entity
            $vendorEntity = \Omnyfy\Vendor\Model\Vendor::ENTITY . '_entity';
            $tableName = $vendorEntity . '_int';
            if (!$setup->tableExists($tableName)) {
                $table = $setup->getConnection()->newTable(
                    $setup->getTable($tableName)
                )
                    ->addColumn(
                        'value_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true],
                        'Value ID'
                    )
                    ->addColumn(
                        'attribute_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Attribute ID'
                    )
                    ->addColumn(
                        'store_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Store ID'
                    )
                    ->addColumn(
                        'entity_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Entity ID'
                    )
                    ->addColumn(
                        'value',
                        Table::TYPE_INTEGER,
                        null,
                        [],
                        'Value'
                    )
                    ->addIndex(
                        $setup->getIdxName(
                            $tableName,
                            ['entity_id', 'attribute_id', 'store_id'],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        ['entity_id', 'attribute_id', 'store_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    )
                    ->addIndex(
                        $setup->getIdxName($tableName, ['attribute_id']),
                        ['attribute_id']
                    )
                    ->addIndex(
                        $setup->getIdxName($tableName, ['store_id']),
                        ['store_id']
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'attribute_id', 'eav_attribute', 'attribute_id'),
                        'attribute_id',
                        $setup->getTable('eav_attribute'),
                        'attribute_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'entity_id', $vendorEntity, 'entity_id'),
                        'entity_id',
                        $setup->getTable($vendorEntity),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'store_id', 'store', 'store_id'),
                        'store_id',
                        $setup->getTable('store'),
                        'store_id',
                        Table::ACTION_CASCADE
                    )
                    ->setComment('Omnyfy Vendor Integer Attribute Backend Table')
                ;
                $setup->getConnection()->createTable($table);
            }
            $tableName = $vendorEntity . '_datetime';
            if (!$setup->tableExists($tableName)) {
                $table = $setup->getConnection()->newTable(
                    $setup->getTable($tableName)
                )
                    ->addColumn(
                        'value_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true],
                        'Value ID'
                    )
                    ->addColumn(
                        'attribute_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Attribute ID'
                    )
                    ->addColumn(
                        'store_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Store ID'
                    )
                    ->addColumn(
                        'entity_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Entity ID'
                    )
                    ->addColumn(
                        'value',
                        Table::TYPE_DATETIME,
                        null,
                        [],
                        'Value'
                    )
                    ->addIndex(
                        $setup->getIdxName(
                            $tableName,
                            ['entity_id', 'attribute_id', 'store_id'],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        ['entity_id', 'attribute_id', 'store_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    )
                    ->addIndex(
                        $setup->getIdxName($tableName, ['attribute_id']),
                        ['attribute_id']
                    )
                    ->addIndex(
                        $setup->getIdxName($tableName, ['store_id']),
                        ['store_id']
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'attribute_id', 'eav_attribute', 'attribute_id'),
                        'attribute_id',
                        $setup->getTable('eav_attribute'),
                        'attribute_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'entity_id', $vendorEntity, 'entity_id'),
                        'entity_id',
                        $setup->getTable($vendorEntity),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'store_id', 'store', 'store_id'),
                        'store_id',
                        $setup->getTable('store'),
                        'store_id',
                        Table::ACTION_CASCADE
                    )
                    ->setComment('Omnyfy Vendor Datetime Attribute Backend Table')
                ;
                $setup->getConnection()->createTable($table);
            }
            $tableName = $vendorEntity . '_decimal';
            if (!$setup->tableExists($tableName)) {
                $table = $setup->getConnection()->newTable(
                    $setup->getTable($tableName)
                )
                    ->addColumn(
                        'value_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true],
                        'Value ID'
                    )
                    ->addColumn(
                        'attribute_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Attribute ID'
                    )
                    ->addColumn(
                        'store_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Store ID'
                    )
                    ->addColumn(
                        'entity_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Entity ID'
                    )
                    ->addColumn(
                        'value',
                        Table::TYPE_DECIMAL,
                        '12,4',
                        [],
                        'Value'
                    )
                    ->addIndex(
                        $setup->getIdxName(
                            $tableName,
                            ['entity_id', 'attribute_id', 'store_id'],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        ['entity_id', 'attribute_id', 'store_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    )
                    ->addIndex(
                        $setup->getIdxName($tableName, ['attribute_id']),
                        ['attribute_id']
                    )
                    ->addIndex(
                        $setup->getIdxName($tableName, ['store_id']),
                        ['store_id']
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'attribute_id', 'eav_attribute', 'attribute_id'),
                        'attribute_id',
                        $setup->getTable('eav_attribute'),
                        'attribute_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'entity_id', $vendorEntity, 'entity_id'),
                        'entity_id',
                        $setup->getTable($vendorEntity),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'store_id', 'store', 'store_id'),
                        'store_id',
                        $setup->getTable('store'),
                        'store_id',
                        Table::ACTION_CASCADE
                    )
                    ->setComment('Omnyfy Vendor Decimal Attribute Backend Table')
                ;
                $setup->getConnection()->createTable($table);
            }
            $tableName = $vendorEntity . '_varchar';
            if (!$setup->tableExists($tableName)) {
                $table = $setup->getConnection()->newTable(
                    $setup->getTable($tableName)
                )
                    ->addColumn(
                        'value_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true],
                        'Value ID'
                    )
                    ->addColumn(
                        'attribute_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Attribute ID'
                    )
                    ->addColumn(
                        'store_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Store ID'
                    )
                    ->addColumn(
                        'entity_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Entity ID'
                    )
                    ->addColumn(
                        'value',
                        Table::TYPE_TEXT,
                        255,
                        [],
                        'Value'
                    )
                    ->addIndex(
                        $setup->getIdxName(
                            $tableName,
                            ['entity_id', 'attribute_id', 'store_id'],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        ['entity_id', 'attribute_id', 'store_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    )
                    ->addIndex(
                        $setup->getIdxName($tableName, ['attribute_id']),
                        ['attribute_id']
                    )
                    ->addIndex(
                        $setup->getIdxName($tableName, ['store_id']),
                        ['store_id']
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'attribute_id', 'eav_attribute', 'attribute_id'),
                        'attribute_id',
                        $setup->getTable('eav_attribute'),
                        'attribute_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'entity_id', $vendorEntity, 'entity_id'),
                        'entity_id',
                        $setup->getTable($vendorEntity),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'store_id', 'store', 'store_id'),
                        'store_id',
                        $setup->getTable('store'),
                        'store_id',
                        Table::ACTION_CASCADE
                    )
                    ->setComment('Omnyfy Vendor Varchar Attribute Backend Table')
                ;
                $setup->getConnection()->createTable($table);
            }
            $tableName = $vendorEntity . '_text';
            if (!$setup->tableExists($tableName)) {
                $table = $setup->getConnection()->newTable(
                    $setup->getTable($tableName)
                )
                    ->addColumn(
                        'value_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true],
                        'Value ID'
                    )
                    ->addColumn(
                        'attribute_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Attribute ID'
                    )
                    ->addColumn(
                        'store_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Store ID'
                    )
                    ->addColumn(
                        'entity_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Entity ID'
                    )
                    ->addColumn(
                        'value',
                        Table::TYPE_TEXT,
                        '64k',
                        [],
                        'Value'
                    )
                    ->addIndex(
                        $setup->getIdxName(
                            $tableName,
                            ['entity_id', 'attribute_id', 'store_id'],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        ['entity_id', 'attribute_id', 'store_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    )
                    ->addIndex(
                        $setup->getIdxName($tableName, ['attribute_id']),
                        ['attribute_id']
                    )
                    ->addIndex(
                        $setup->getIdxName($tableName, ['store_id']),
                        ['store_id']
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'attribute_id', 'eav_attribute', 'attribute_id'),
                        'attribute_id',
                        $setup->getTable('eav_attribute'),
                        'attribute_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'entity_id', $vendorEntity, 'entity_id'),
                        'entity_id',
                        $setup->getTable($vendorEntity),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName($tableName, 'store_id', 'store', 'store_id'),
                        'store_id',
                        $setup->getTable('store'),
                        'store_id',
                        Table::ACTION_CASCADE
                    )
                    ->setComment('Omnyfy Vendor Text Attribute Backend Table')
                ;
                $setup->getConnection()->createTable($table);
            }

        }

        if (version_compare($version, '1.0.3', '<')) {
            $conn = $setup->getConnection();

            if ($setup->tableExists('omnyfy_vendor') && $conn->tableColumnExists('omnyfy_vendor', 'vendor_id')) {
                $conn->changeColumn(
                    'omnyfy_vendor',
                    'vendor_id',
                    'entity_id',
                    'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT'
                );
            }

            if ($setup->tableExists('omnyfy_location') && $conn->tableColumnExists('omnyfy_location', 'location_id')) {
                $conn->changeColumn(
                    'omnyfy_location',
                    'location_id',
                    'entity_id',
                    'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT'
                );
            }

            $toRenameTables = [];
            $vendorTables = [
                'omnyfy_vendor' => 'omnyfy_vendor_vendor_entity',
                'omnyfy_vendor_datetime' => 'omnyfy_vendor_vendor_entity_datetime',
                'omnyfy_vendor_decimal' => 'omnyfy_vendor_vendor_entity_decimal',
                'omnyfy_vendor_int' => 'omnyfy_vendor_vendor_entity_int',
                'omnyfy_vendor_text' => 'omnyfy_vendor_vendor_entity_text',
                'omnyfy_vendor_varchar' => 'omnyfy_vendor_vendor_entity_varchar',
                'omnyfy_vendor_vendor_datetime' => 'omnyfy_vendor_vendor_entity_datetime',
                'omnyfy_vendor_vendor_decimal' => 'omnyfy_vendor_vendor_entity_decimal',
                'omnyfy_vendor_vendor_int' => 'omnyfy_vendor_vendor_entity_int',
                'omnyfy_vendor_vendor_text' => 'omnyfy_vendor_vendor_entity_text',
                'omnyfy_vendor_vendor_varchar' => 'omnyfy_vendor_vendor_entity_varchar',
            ];
            $locationTables = [
                'omnyfy_location' => 'omnyfy_vendor_location_entity',
                'omnyfy_location_datetime' => 'omnyfy_vendor_location_entity_datetime',
                'omnyfy_location_decimal' => 'omnyfy_vendor_location_entity_decimal',
                'omnyfy_location_int' => 'omnyfy_vendor_location_entity_int',
                'omnyfy_location_text' => 'omnyfy_vendor_location_entity_text',
                'omnyfy_location_varchar' => 'omnyfy_vendor_location_entity_varchar',
            ];
            $otherTables = [
                'omnyfy_admin_user_vendor' => 'omnyfy_vendor_vendor_admin_user',
                'omnyfy_customer_vendor' => 'omnyfy_vendor_vendor_customer',
                'omnyfy_inventory' => 'omnyfy_vendor_inventory',
                'omnyfy_invoice_vendor' => 'omnyfy_vendor_vendor_invoice',
                'omnyfy_order_vendor' => 'omnyfy_vendor_vendor_order',
                'omnyfy_product_vendor' => 'omnyfy_vendor_vendor_product'
            ];
            foreach($vendorTables as $old => $new) {
                if ($setup->tableExists($conn->getTableName($old)) && !$setup->tableExists($conn->getTableName($new))) {
                    $toRenameTables[] = [
                        'oldName' => $conn->getTableName($old),
                        'newName' => $conn->getTableName($new)
                    ];
                }
            }
            if (!empty($toRenameTables)) {
                $conn->renameTablesBatch($toRenameTables);
            }
            $toRenameTables = [];
            foreach($locationTables as $old => $new) {
                if ($setup->tableExists($conn->getTableName($old)) && !$setup->tableExists($conn->getTableName($new))) {
                    $toRenameTables[] = [
                        'oldName' => $conn->getTableName($old),
                        'newName' => $conn->getTableName($new)
                    ];
                }
            }
            if (!empty($toRenameTables)) {
                $conn->renameTablesBatch($toRenameTables);
            }
            $toRenameTables = [];
            foreach($otherTables as $old => $new) {
                if ($setup->tableExists($conn->getTableName($old)) && !$setup->tableExists($conn->getTableName($new))) {
                    $toRenameTables[] = [
                        'oldName' => $conn->getTableName($old),
                        'newName' => $conn->getTableName($new)
                    ];
                }
            }
            if (!empty($toRenameTables)) {
                $conn->renameTablesBatch($toRenameTables);
            }

            foreach($vendorTables as $old => $table) {
                $table = $conn->getTableName($table);
                if ('omnyfy_vendor' == $old) {
                    if ($setup->tableExists($table) && $conn->tableColumnExists($table, 'vendor_id')) {
                        $conn->changeColumn(
                            $table,
                            'vendor_id',
                            'entity_id',
                            'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT'
                        );
                    }
                }
                else{
                    if ($setup->tableExists($table) && $conn->tableColumnExists($table, 'vendor_id')) {
                        $conn->changeColumn(
                            $table,
                            'vendor_id',
                            'entity_id',
                            "INT(10) UNSIGNED NOT NULL DEFAULT '0'"
                        );
                    }
                }
            }

            foreach($locationTables as $old => $table) {
                $table = $conn->getTableName($table);
                if ('omnyfy_location' == $old) {
                    if ($setup->tableExists($table) && $conn->tableColumnExists($table, 'location_id')) {
                        $conn->changeColumn(
                            $table,
                            'location_id',
                            'entity_id',
                            'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT'
                        );
                    }
                }
                else {
                    if ($setup->tableExists($table) && $conn->tableColumnExists($table, 'location_id')) {
                        $conn->changeColumn(
                            $table,
                            'location_id',
                            'entity_id',
                            "INT(10) UNSIGNED NOT NULL DEFAULT '0'"
                        );
                    }
                }
            }
        }

        if (version_compare($version, '1.0.4', '<')) {
            $conn = $setup->getConnection();
            $locationTables = ['omnyfy_location', 'omnyfy_vendor_location_entity'];
            foreach($locationTables as $table) {
                $tableName = $conn->getTableName($table);
                if ($setup->tableExists($tableName) && $conn->tableColumnExists($tableName, 'latitude')) {
                    $conn->changeColumn(
                        $tableName,
                        'latitude',
                        'lat',
                        "DECIMAL(10,6) NOT NULL"
                    );
                }
                if ($setup->tableExists($tableName) && $conn->tableColumnExists($tableName, 'longitude')) {
                    $conn->changeColumn(
                        $tableName,
                        'longitude',
                        'lon',
                        "DECIMAL(10,6) NOT NULL"
                    );
                }
            }
        }

        if (version_compare($version, '1.0.5', '<')) {
            $conn = $setup->getConnection();
            $tableName = $conn->getTableName('omnyfy_vendor_order_total');
            if ($setup->tableExists($tableName) && ! $conn->tableColumnExists($tableName, 'shipping_tax')) {
                $conn->addColumn(
                    $tableName,
                    'shipping_tax',
                    [
                        'type'      => Table::TYPE_DECIMAL,
                        'length'    => '12,4',
                        'nullable'  => false,
                        'default'   => 0.0,
                        'comment'   => 'Shipping Tax',
                    ]
                );
            }
        }

        if (version_compare($version, '1.0.6', '<')) {
            $conn = $setup->getConnection();
            $tableName = $conn->getTableName('omnyfy_vendor_order_total');
            if ($setup->tableExists($tableName)) {
                if (! $conn->tableColumnExists($tableName, 'base_subtotal')) {
                    $conn->addColumn(
                        $tableName,
                        'base_subtotal',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Base Subtotal',
                            'after'     => 'subtotal'
                        ]
                    );
                }
                if (! $conn->tableColumnExists($tableName, 'subtotal_incl_tax')) {
                    $conn->addColumn(
                        $tableName,
                        'subtotal_incl_tax',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Subtotal including Tax',
                            'after'     => 'base_subtotal'
                        ]
                    );
                }
                if (! $conn->tableColumnExists($tableName, 'base_subtotal_incl_tax')) {
                    $conn->addColumn(
                        $tableName,
                        'base_subtotal_incl_tax',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Base Subtotal including Tax',
                            'after'     => 'subtotal_incl_tax'
                        ]
                    );
                }
                if (! $conn->tableColumnExists($tableName, 'base_tax_amount')) {
                    $conn->addColumn(
                        $tableName,
                        'base_tax_amount',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Base Tax Amount',
                            'after'     => 'tax_amount'
                        ]
                    );
                }

                if (! $conn->tableColumnExists($tableName, 'base_discount_amount')) {
                    $conn->addColumn(
                        $tableName,
                        'base_discount_amount',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Base Discount Amount',
                            'after'     => 'discount_amount'
                        ]
                    );
                }
                if (! $conn->tableColumnExists($tableName, 'base_shipping_amount')) {
                    $conn->addColumn(
                        $tableName,
                        'base_shipping_amount',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Base Shipping Amount',
                            'after'     => 'shipping_amount'
                        ]
                    );
                }

                if (! $conn->tableColumnExists($tableName, 'shipping_incl_tax')) {
                    $conn->addColumn(
                        $tableName,
                        'shipping_incl_tax',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Shipping including Tax',
                            'after'     => 'base_shipping_amount'
                        ]
                    );
                }

                if (! $conn->tableColumnExists($tableName, 'base_shipping_incl_tax')) {
                    $conn->addColumn(
                        $tableName,
                        'base_shipping_incl_tax',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Base Shipping including Tax',
                            'after'     => 'shipping_incl_tax'
                        ]
                    );
                }

                if (! $conn->tableColumnExists($tableName, 'base_shipping_tax')) {
                    $conn->addColumn(
                        $tableName,
                        'base_shipping_tax',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Base Shipping Tax',
                            'after'     => 'shipping_tax'
                        ]
                    );
                }

                if (! $conn->tableColumnExists($tableName, 'grand_total')) {
                    $conn->addColumn(
                        $tableName,
                        'grand_total',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Grand Total',
                            'after'     => 'base_shipping_tax'
                        ]
                    );
                }
                if (! $conn->tableColumnExists($tableName, 'base_grand_total')) {
                    $conn->addColumn(
                        $tableName,
                        'base_grand_total',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Base Grand Total',
                            'after'     => 'grand_total'
                        ]
                    );
                }
            }

            $invoiceTable = $conn->getTableName('omnyfy_vendor_invoice_total');
            if ($setup->tableExists($invoiceTable)) {
                if (!$conn->tableColumnExists($invoiceTable, 'base_subtotal')) {
                    $conn->addColumn(
                        $invoiceTable,
                        'base_subtotal',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Base Subtotal',
                            'after'     => 'subtotal'
                        ]
                    );
                }

                if (!$conn->tableColumnExists($invoiceTable, 'subtotal_incl_tax')) {
                    $conn->addColumn(
                        $invoiceTable,
                        'subtotal_incl_tax',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Subtotal including Tax',
                            'after'     => 'base_subtotal'
                        ]
                    );
                }

                if (!$conn->tableColumnExists($invoiceTable, 'base_subtotal_incl_tax')) {
                    $conn->addColumn(
                        $invoiceTable,
                        'base_subtotal_incl_tax',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Base Subtotal including Tax',
                            'after'     => 'subtotal_incl_tax'
                        ]
                    );
                }

                if (!$conn->tableColumnExists($invoiceTable, 'base_tax_amount')) {
                    $conn->addColumn(
                        $invoiceTable,
                        'base_tax_amount',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Base Tax Amount',
                            'after'     => 'tax_amount'
                        ]
                    );
                }

                if (!$conn->tableColumnExists($invoiceTable, 'base_discount_amount')) {
                    $conn->addColumn(
                        $invoiceTable,
                        'base_discount_amount',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Base Discount Amount',
                            'after'     => 'discount_amount'
                        ]
                    );
                }

                if (!$conn->tableColumnExists($invoiceTable, 'base_shipping_amount')) {
                    $conn->addColumn(
                        $invoiceTable,
                        'base_shipping_amount',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Base Shipping Amount',
                            'after'     => 'shipping_amount'
                        ]
                    );
                }

                if (!$conn->tableColumnExists($invoiceTable, 'shipping_incl_tax')) {
                    $conn->addColumn(
                        $invoiceTable,
                        'shipping_incl_tax',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Shipping including Tax',
                            'after'     => 'shipping_amount'
                        ]
                    );
                }

                if (!$conn->tableColumnExists($invoiceTable, 'base_shipping_incl_tax')) {
                    $conn->addColumn(
                        $invoiceTable,
                        'base_shipping_incl_tax',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Base Shipping including Tax',
                            'after'     => 'shipping_incl_tax'
                        ]
                    );
                }

                if (!$conn->tableColumnExists($invoiceTable, 'shipping_tax')) {
                    $conn->addColumn(
                        $invoiceTable,
                        'shipping_tax',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Shipping Tax',
                            'after'     => 'base_shipping_incl_tax'
                        ]
                    );
                }

                if (!$conn->tableColumnExists($invoiceTable, 'base_shipping_tax')) {
                    $conn->addColumn(
                        $invoiceTable,
                        'base_shipping_tax',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Base Shipping Tax',
                            'after'     => 'shipping_tax'
                        ]
                    );
                }

                if (!$conn->tableColumnExists($invoiceTable, 'grand_total')) {
                    $conn->addColumn(
                        $invoiceTable,
                        'grand_total',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Grand Total',
                            'after'     => 'base_shipping_tax'
                        ]
                    );
                }

                if (!$conn->tableColumnExists($invoiceTable, 'base_grand_total')) {
                    $conn->addColumn(
                        $invoiceTable,
                        'base_grand_total',
                        [
                            'type'      => Table::TYPE_DECIMAL,
                            'length'    => '12,4',
                            'nullable'  => false,
                            'default'   => 0.0,
                            'comment'   => 'Base Grand Total',
                            'after'     => 'grand_total'
                        ]
                    );
                }
            }
        }

        if (version_compare($version, '1.0.7', '<')) {
            $conn = $setup->getConnection();

            $tableName = $conn->getTableName('omnyfy_vendor_location_entity');
            if ($setup->tableExists($tableName) && $conn->tableColumnExists($tableName, 'description')) {
                $conn->changeColumn(
                    $tableName,
                    'description',
                    'description',
                    "TEXT"
                );
            }
        }

        if (version_compare($version, '1.0.9', '<')) {
            $conn = $setup->getConnection();

            $tableName = $conn->getTableName('omnyfy_vendor_inventory');
            if ($setup->tableExists($tableName) && !$conn->tableColumnExists($tableName, 'inventory_id')) {
                $conn->addColumn(
                    $tableName,
                    'inventory_id',
                    [
                        'type'      => Table::TYPE_INTEGER,
                        'length'    => null,
                        'nullable'  => false,
                        'unsigned'  => true,
                        'identity'  => true,
                        'primary'   => true,
                        'comment'   => 'ID',
                        'first'     => true
                    ]
                );
            }
        }

        if (version_compare($version, '1.0.10', '<')) {
            $conn = $setup->getConnection();

            $tableName = $conn->getTableName('omnyfy_vendor_location_entity');
            if ($setup->tableExists($tableName) && !$conn->tableColumnExists($tableName, 'is_warehouse')) {
                $conn->addColumn(
                    $tableName,
                    'is_warehouse',
                    [
                        'type'      => Table::TYPE_SMALLINT,
                        'length'    => 1,
                        'nullable'  => false,
                        'unsigned'  => true,
                        'default'   => 0,
                        'comment'   => 'Is Warehouse'
                    ]
                );
            }
        }

        if (version_compare($version, '1.0.11', '<')) {
            $conn = $setup->getConnection();
            $salesRuleTable = $conn->getTableName('salesrule');
            if ($setup->tableExists($salesRuleTable)) {
                if (!$conn->tableColumnExists($salesRuleTable, 'location_id')) {
                    $conn->addColumn(
                        $salesRuleTable,
                        'location_id',
                        [
                            'type' => Table::TYPE_INTEGER,
                            'nullable' => true,
                            'comment' => 'Location ID',
                            'unsigned' => true,
                            'default' => null,
                        ]
                    );

                    $conn->addForeignKey(
                        $setup->getFkName('salesrule', 'location_id', 'omnyfy_vendor_location_entity', 'entity_id'),
                        $salesRuleTable,
                        'location_id',
                        $setup->getTable('omnyfy_vendor_location_entity'),
                        'entity_id',
                        Table::ACTION_SET_NULL
                    );
                }

                if (!$conn->tableColumnExists($salesRuleTable, 'vendor_id')) {
                    $conn->addColumn(
                        $salesRuleTable,
                        'vendor_id',
                        [
                            'type' => Table::TYPE_INTEGER,
                            'nullable' => true,
                            'comment' => 'Vendor ID',
                            'unsigned' => true,
                        ]
                    );

                    $conn->addForeignKey(
                        $setup->getFkName('salesrule', 'vendor_id', 'omnyfy_vendor_vendor_entity', 'entity_id'),
                        $salesRuleTable,
                        'vendor_id',
                        $setup->getTable('omnyfy_vendor_vendor_entity'),
                        'entity_id',
                        Table::ACTION_SET_NULL
                    );
                }
            }
        }

        if (version_compare($version, '1.0.12', '<')) {
            $conn = $setup->getConnection();

            $tableName = $conn->getTableName('omnyfy_vendor_customer_favorite_vendor');
            if (!$setup->tableExists($tableName)) {
                $customerVendorTable = $setup->getConnection()->newTable(
                    $tableName
                )
                    ->addColumn(
                        'id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                        'SEQ ID'
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
                        $setup->getIdxName(
                            $tableName,
                            ['customer_id'],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        ['customer_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    )
                    ->addForeignKey(
                        $setup->getFkName(
                            $tableName,
                            'customer_id',
                            'customer_entity',
                            'entity_id'
                        ),
                        'customer_id',
                        $setup->getTable('customer_entity'),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName(
                            $tableName,
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
                $setup->getConnection()->createTable($customerVendorTable);
            }
        }

        if (version_compare($version, '1.0.13', '<')) {
            $conn = $setup->getConnection();

            $tableName = $conn->getTableName('omnyfy_vendor_eav_attribute');
            if ($setup->tableExists($tableName)) {
                if (!$conn->tableColumnExists($tableName, 'used_for_sort_by')) {
                    $conn->addColumn(
                        $tableName,
                        'used_for_sort_by',
                        [
                            'type' => Table::TYPE_SMALLINT,
                            'length' => '5',
                            'nullable' => false,
                            'unsigned' => true,
                            'default' => 0,
                            'comment' => 'Is Used For Sorting',
                            'after' => 'is_filterable_in_grid'
                        ]
                    );
                }

                if (!$conn->tableColumnExists($tableName, 'is_global')) {
                    $conn->addColumn(
                        $tableName,
                        'is_global',
                        [
                            'type' => Table::TYPE_SMALLINT,
                            'length' => '5',
                            'nullable' => false,
                            'unsigned' => true,
                            'default' => 1,
                            'comment' => 'Is Global',
                            'after' => 'attribute_id'
                        ]
                    );
                }

                if (!$conn->tableColumnExists($tableName, 'is_visible_on_front')) {
                    $conn->addColumn(
                        $tableName,
                        'is_visible_on_front',
                        [
                            'type' => Table::TYPE_SMALLINT,
                            'length' => '5',
                            'nullable' => false,
                            'unsigned' => true,
                            'default' => 0,
                            'comment' => 'Is Visible On Front',
                            'after' => 'is_filterable'
                        ]
                    );
                }

                if (!$conn->tableColumnExists($tableName, 'is_html_allowed_on_front')) {
                    $conn->addColumn(
                        $tableName,
                        'is_html_allowed_on_front',
                        [
                            'type' => Table::TYPE_SMALLINT,
                            'length' => '5',
                            'nullable' => false,
                            'unsigned' => true,
                            'default' => 0,
                            'comment' => 'Is HTML Allowed On Front',
                            'after' => 'is_visible_on_front'
                        ]
                    );
                }

                if (!$conn->tableColumnExists($tableName, 'is_filterable_in_search')) {
                    $conn->addColumn(
                        $tableName,
                        'is_filterable_in_search',
                        [
                            'type' => Table::TYPE_SMALLINT,
                            'length' => '5',
                            'nullable' => false,
                            'unsigned' => true,
                            'default' => 0,
                            'comment' => 'Is Filterable In Search',
                            'after' => 'is_html_allowed_on_front'
                        ]
                    );
                }

                if (!$conn->tableColumnExists($tableName, 'is_visible_in_advanced_search')) {
                    $conn->addColumn(
                        $tableName,
                        'is_visible_in_advanced_search',
                        [
                            'type' => Table::TYPE_SMALLINT,
                            'length' => '5',
                            'nullable' => false,
                            'unsigned' => true,
                            'default' => 0,
                            'comment' => 'Is Visible in Advanced Search',
                            'after' => 'is_filterable_in_search'
                        ]
                    );
                }

                if (!$conn->tableColumnExists($tableName, 'is_wysiwyg_enabled')) {
                    $conn->addColumn(
                        $tableName,
                        'is_wysiwyg_enabled',
                        [
                            'type' => Table::TYPE_SMALLINT,
                            'length' => '5',
                            'nullable' => false,
                            'unsigned' => true,
                            'default' => 0,
                            'comment' => 'Is WYSIWYG Enabled',
                            'after' => 'is_visible_in_advanced_search'
                        ]
                    );
                }

                if (!$conn->tableColumnExists($tableName, 'used_in_form')) {
                    $conn->addColumn(
                        $tableName,
                        'used_in_form',
                        [
                            'type' => Table::TYPE_SMALLINT,
                            'length' => '5',
                            'nullable' => false,
                            'unsigned' => true,
                            'default' => 0,
                            'comment' => 'Is Used In Form',
                            'after' => 'used_for_sort_by'
                        ]
                    );
                }

                //TODO: UPDATE attributes to is_visible = 0
            }

            $tableName = $conn->getTableName('omnyfy_vendor_vendor_type');
            if (!$setup->tableExists($tableName)) {
                $vendorTypeTable = $conn->newTable(
                    $tableName
                )
                    ->addColumn(
                        'type_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                        'Type ID'
                    )
                    ->addColumn(
                        'type_name',
                        Table::TYPE_TEXT,
                        255,
                        ['nullable' => false],
                        'Type Name'
                    )
                    ->addColumn(
                        'search_by',
                        Table::TYPE_SMALLINT,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Search By'
                    )
                    ->addColumn(
                        'view_mode',
                        Table::TYPE_SMALLINT,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'View Mode'
                    )
                    ->addColumn(
                        'vendor_attribute_set_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Vendor Attribute Set ID'
                    )
                    ->addColumn(
                        'location_attribute_set_id',
                        Table::TYPE_SMALLINT,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Location Attribute Set ID'
                    )
                    ->addColumn(
                        'status',
                        Table::TYPE_SMALLINT,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'status'
                    )
                    ->addIndex(
                        $setup->getIdxName(
                            $tableName,
                            ['type_id', 'vendor_attribute_set_id'],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        ['type_id', 'vendor_attribute_set_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    )
                    ->addIndex(
                        $setup->getIdxName(
                            $tableName,
                            ['type_id', 'location_attribute_set_id'],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        ['type_id', 'location_attribute_set_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    )
                    ->addForeignKey(
                        $setup->getFkName(
                            $tableName,
                            'vendor_attribute_set_id',
                            'eav_attribute_set',
                            'attribute_set_id'
                        ),
                        'vendor_attribute_set_id',
                        $setup->getTable('eav_attribute_set'),
                        'attribute_set_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName(
                            $tableName,
                            'location_attribute_set_id',
                            'eav_attribute_set',
                            'attribute_set_id'
                        ),
                        'location_attribute_set_id',
                        $setup->getTable('eav_attribute_set'),
                        'attribute_set_id',
                        Table::ACTION_CASCADE
                    )
                ;
                $conn->createTable($vendorTypeTable);

                //Add default vendor type.
                $vendorDefaultAttributeSetId = $this->getEntityDefaultAttributeSetId(
                    \Omnyfy\Vendor\Model\Vendor::ENTITY,
                    $conn
                );

                $locationDefaultAttributeSetId = $this->getEntityDefaultAttributeSetId(
                    \Omnyfy\Vendor\Model\Location::ENTITY,
                    $conn
                );

                $conn->insert($tableName, [
                    'type_id' => 1,
                    'type_name' => 'Default',
                    'search_by' => 0,
                    'view_mode' => 0,
                    'vendor_attribute_set_id' => $vendorDefaultAttributeSetId,
                    'location_attribute_set_id' => $locationDefaultAttributeSetId,
                    'status' => 1
                ]);
            }

            $vendorTable = $conn->getTableName('omnyfy_vendor_vendor_entity');
            if ($setup->tableExists($vendorTable)) {
                if (!$conn->tableColumnExists($vendorTable, 'updated_at')) {
                    $conn->addColumn(
                        $vendorTable,
                        'updated_at',
                        [
                            'type' => Table::TYPE_TIMESTAMP,
                            'nullable' => false,
                            'Comment' => 'Updated Time',
                            'default' => Table::TIMESTAMP_INIT_UPDATE,
                            'after' => 'email'
                        ]
                    );
                }

                if (!$conn->tableColumnExists($vendorTable, 'created_at')) {
                    $conn->addColumn(
                        $vendorTable,
                        'create_at',
                        [
                            'type' => Table::TYPE_TIMESTAMP,
                            'nullable' => false,
                            'Comment' => 'Created Time',
                            'default' => Table::TIMESTAMP_INIT,
                            'after' => 'email'
                        ]
                    );
                }

                if (!$conn->tableColumnExists($vendorTable, 'type_id')) {
                    $conn->addColumn(
                        $vendorTable,
                        'type_id',
                        [
                            'type' => Table::TYPE_INTEGER,
                            'nullable' => true,
                            'comment' => 'Vendor Type ID',
                            'unsigned' => true,
                            'default' => null,
                        ]
                    );

                    $conn->addForeignKey(
                        $setup->getFkName(
                            'omnyfy_vendor_vendor_entity',
                            'type_id',
                            'omnyfy_vendor_vendor_type',
                            'type_id'
                        ),
                        $vendorTable,
                        'type_id',
                        $setup->getTable('omnyfy_vendor_vendor_type'),
                        'type_id',
                        Table::ACTION_SET_NULL
                    );

                    $conn->update($vendorTable, ['type_id' => 1]);
                }

                if (!$conn->tableColumnExists($vendorTable, 'attribute_set_id')) {
                    $conn->addColumn(
                        $vendorTable,
                        'attribute_set_id',
                        [
                            'type' => Table::TYPE_SMALLINT,
                            'nullable' => true,
                            'comment' => 'Attribute Set ID',
                            'unsigned' => true,
                            'default' => null,
                        ]
                    );

                    $conn->addForeignKey(
                        $setup->getFkName(
                            'omnyfy_vendor_vendor_entity',
                            'attribute_set_id',
                            'eav_attribute_set',
                            'attribute_set_id'
                        ),
                        $vendorTable,
                        'attribute_set_id',
                        $setup->getTable('eav_attribute_set'),
                        'attribute_set_id',
                        Table::ACTION_SET_NULL
                    );

                    $vendorDefaultAttributeSetId = $this->getEntityDefaultAttributeSetId(
                        \Omnyfy\Vendor\Model\Vendor::ENTITY,
                        $conn
                    );
                    $conn->update($vendorTable, ['attribute_set_id' => $vendorDefaultAttributeSetId]);
                }

                $columns = [
                    'shipping_policy', 'return_policy', 'payment_policy', 'marketing_policy', 'description'
                ];
                $eavAttrTable = $conn->getTableName('eav_attribute');
                $vendorEntityTypeId = $this->getEntityTypeId(\Omnyfy\Vendor\Model\Vendor::ENTITY, $conn);
                $valueTable = $conn->getTableName('omnyfy_vendor_vendor_entity_text');
                foreach($columns as $column) {
                    if ($conn->tableColumnExists($vendorTable, $column) && !empty($vendorEntityTypeId)) {
                        //get attribute_id
                        $attributeId = $this->getAttributeId($column, $vendorEntityTypeId, $eavAttrTable, $conn);
                        if (empty($attributeId)) {
                            continue;
                        }

                        //insert into text value table
                        $select = $conn->select()
                            ->from($vendorTable,
                                [
                                    'attribute_id' => new \Zend_Db_Expr($attributeId),
                                    'store_id' => new \Zend_Db_Expr(0),
                                    'entity_id'=> 'entity_id',
                                    'value' => $column
                                ]
                            )
                        ;
                        $sql = $conn->insertFromSelect($select, $valueTable,
                            [
                                'attribute_id',
                                'store_id',
                                'entity_id',
                                'value'
                            ],
                            2
                        );
                        $setup->run($sql);

                        //update attribute backend type
                        $this->updateAttribute($attributeId, 'backend_type', 'text', $eavAttrTable, $conn);

                        //remove column
                        $conn->dropColumn($vendorTable, $column);
                    }
                }

                $columns = [
                    'address', 'phone', 'fax', 'social_media', 'abn', 'logo', 'banner'
                ];
                $eavAttrTable = $conn->getTableName('eav_attribute');
                $vendorEntityTypeId = $this->getEntityTypeId(\Omnyfy\Vendor\Model\Vendor::ENTITY, $conn);
                $valueTable = $conn->getTableName('omnyfy_vendor_vendor_entity_varchar');
                foreach($columns as $column) {
                    if ($conn->tableColumnExists($vendorTable, $column) && !empty($vendorEntityTypeId)) {
                        //get attribute_id
                        $attributeId = $this->getAttributeId($column, $vendorEntityTypeId, $eavAttrTable, $conn);
                        if (empty($attributeId)) {
                            continue;
                        }

                        //insert into text value table
                        $select = $conn->select()
                            ->from($vendorTable,
                                [
                                    'attribute_id' => new \Zend_Db_Expr($attributeId),
                                    'store_id' => new \Zend_Db_Expr(0),
                                    'entity_id'=> 'entity_id',
                                    'value' => $column
                                ]
                            )
                        ;
                        $sql = $conn->insertFromSelect($select, $valueTable,
                            [
                                'attribute_id',
                                'store_id',
                                'entity_id',
                                'value'
                            ],
                            2
                        );
                        $setup->run($sql);

                        //update attribute backend type
                        $this->updateAttribute($attributeId, 'backend_type', 'varchar', $eavAttrTable, $conn);

                        //remove column
                        $conn->dropColumn($vendorTable, $column);
                    }
                }

                $staticFields = ['name', 'status', 'email'];
                $vendorAttrTable = $conn->getTableName('omnyfy_vendor_eav_attribute');
                foreach($staticFields as $attributeCode) {
                    $attributeId = $this->getAttributeId($attributeCode, $vendorEntityTypeId, $eavAttrTable, $conn);
                    if (empty($attributeId)) {
                        continue;
                    }
                    $this->updateAttribute($attributeId, 'is_visible', '0', $vendorAttrTable, $conn);
                }
            }

            $locationTable = $conn->getTableName('omnyfy_vendor_location_entity');
            if ($setup->tableExists($locationTable)) {
                if (!$conn->tableColumnExists($locationTable, 'vendor_type_id')) {
                    $conn->addColumn(
                        $locationTable,
                        'vendor_type_id',
                        [
                            'type' => Table::TYPE_INTEGER,
                            'nullable' => true,
                            'comment' => 'Vendor Type ID',
                            'unsigned' => true,
                            'default' => null,
                        ]
                    );

                    $conn->addForeignKey(
                        $setup->getFkName(
                            'omnyfy_vendor_location_entity',
                            'vendor_type_id',
                            'omnyfy_vendor_vendor_type',
                            'type_id'
                        ),
                        $locationTable,
                        'vendor_type_id',
                        $setup->getTable('omnyfy_vendor_vendor_type'),
                        'type_id',
                        Table::ACTION_SET_NULL
                    );

                    $conn->update($locationTable, ['vendor_type_id' => 1]);
                }

                if (!$conn->tableColumnExists($locationTable, 'attribute_set_id')) {
                    $conn->addColumn(
                        $locationTable,
                        'attribute_set_id',
                        [
                            'type' => Table::TYPE_SMALLINT,
                            'nullable' => true,
                            'comment' => 'Attribute Set ID',
                            'unsigned' => true,
                            'default' => null,
                        ]
                    );

                    $conn->addForeignKey(
                        $setup->getFkName(
                            'omnyfy_vendor_location_entity',
                            'attribute_set_id',
                            'eav_attribute_set',
                            'attribute_set_id'
                        ),
                        $locationTable,
                        'attribute_set_id',
                        $setup->getTable('eav_attribute_set'),
                        'attribute_set_id',
                        Table::ACTION_SET_NULL
                    );

                    $locationDefaultAttributeSetId = $this->getEntityDefaultAttributeSetId(
                        \Omnyfy\Vendor\Model\Location::ENTITY,
                        $conn
                    );
                    $conn->update($locationTable, ['attribute_set_id' => $locationDefaultAttributeSetId]);
                }
            }

            $tableName = $conn->getTableName('omnyfy_vendor_related_location');
            if (!$setup->tableExists($tableName)) {
                $relatedLocationTable = $conn->newTable($tableName)
                    ->addColumn(
                        'vendor_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Vendor ID'
                    )
                    ->addColumn(
                        'location_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Location ID'
                    )
                    ->addIndex(
                        $setup->getIdxName(
                            $tableName,
                            ['vendor_id', 'location_id'],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        ['vendor_id', 'location_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    )
                    ->addForeignKey(
                        $setup->getFkName(
                            $tableName,
                            'vendor_id',
                            'omnyfy_vendor_vendor_entity',
                            'entity_id'
                        ),
                        'vendor_id',
                        $setup->getTable('omnyfy_vendor_vendor_entity'),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName(
                            $tableName,
                            'location_id',
                            'omnyfy_vendor_location_entity',
                            'entity_id'
                        ),
                        'location_id',
                        $setup->getTable('omnyfy_vendor_location_entity'),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                    ;

                $conn->createTable($relatedLocationTable);
            }

        }

        if (version_compare($version, '1.0.14', '<')) {
            $conn = $setup->getConnection();

            $tableName = $conn->getTableName('omnyfy_vendor_location_entity');
            if ($setup->tableExists($tableName)) {
                if (!$conn->tableColumnExists($tableName, 'rad_lon')) {
                    $conn->addColumn(
                        $tableName,
                        'rad_lon',
                        [
                            'type' => Table::TYPE_DECIMAL,
                            'length' => '10,6',
                            'nullable' => false,
                            'default' => 0,
                            'comment' => 'Radians of longitude',
                            'after' => 'lon'
                        ]
                    );
                }
                if (!$conn->tableColumnExists($tableName, 'rad_lat')) {
                    $conn->addColumn(
                        $tableName,
                        'rad_lat',
                        [
                            'type' => Table::TYPE_DECIMAL,
                            'length' => '10,6',
                            'nullable' => false,
                            'default' => 0,
                            'comment' => 'Radians of latitude',
                            'after' => 'rad_lon'
                        ]
                    );
                }
                if (!$conn->tableColumnExists($tableName, 'cos_lat')) {
                    $conn->addColumn(
                        $tableName,
                        'cos_lat',
                        [
                            'type' => Table::TYPE_DECIMAL,
                            'length' => '10,6',
                            'nullable' => false,
                            'default' => 0,
                            'comment' => 'Cosine of latitude',
                            'after' => 'rad_lat'
                        ]
                    );
                }
                if (!$conn->tableColumnExists($tableName, 'sin_lat')) {
                    $conn->addColumn(
                        $tableName,
                        'sin_lat',
                        [
                            'type' => Table::TYPE_DECIMAL,
                            'length' => '10,6',
                            'nullable' => false,
                            'default' => 0,
                            'comment' => 'Sine of latitude',
                            'after' => 'cos_lat'
                        ]
                    );
                }
            }
        }

        if (version_compare($version, '1.0.15', '<')) {
            $conn = $setup->getConnection();

            $tableName = $conn->getTableName('omnyfy_vendor_eav_attribute');
            if ($setup->tableExists($tableName)) {
                if ($conn->tableColumnExists($tableName, 'is_visible')) {
                    $conn->modifyColumn($tableName, 'is_visible',
                        [
                            'type' => Table::TYPE_SMALLINT,
                            'unsigned' => true,
                            'nullable'=> false,
                            'default' => 1,
                            'comment' => 'Is Visible'
                        ]
                    );
                }
            }
        }

        if (version_compare($version, '1.0.16', '<')) {
            $conn = $setup->getConnection();

            $tableName = $conn->getTableName('omnyfy_vendor_quote_shipping');
            if (!$setup->tableExists($tableName)) {
                $quoteShippingTable = $conn->newTable($tableName)
                    ->addColumn(
                        'quote_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Quote ID'
                    )
                    ->addColumn(
                        'address_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Address ID'
                    )
                    ->addColumn(
                        'location_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Location ID'
                    )
                    ->addColumn(
                        'rate_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => true, 'unsigned' => true],
                        'Rate ID'
                    )
                    ->addColumn(
                        'method_code',
                        Table::TYPE_TEXT,
                        255,
                        ['nullable' => false, 'default' => ''],
                        'Method Code'
                    )
                    ->addColumn(
                        'amount',
                        Table::TYPE_DECIMAL,
                        '12,4',
                        ['nullable' => false, 'default' => 0.0],
                        'Amount'
                    )
                    ->addColumn(
                        'base_amount',
                        Table::TYPE_DECIMAL,
                        '12,4',
                        ['nullable' => false, 'default' => 0.0],
                        'Base Amount'
                    )
                    ->addColumn(
                        'carrier',
                        Table::TYPE_TEXT,
                        255,
                        ['nullable' => false, 'default' => ''],
                        'Carrier'
                    )
                    ->addColumn(
                        'method_title',
                        Table::TYPE_TEXT,
                        255,
                        ['nullable' => false, 'default' => ''],
                        'Method Title'
                    )
                    ->addIndex(
                        $setup->getIdxName(
                            $tableName,
                            ['quote_id', 'location_id'],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        ['quote_id', 'location_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    )
                    ->addForeignKey(
                        $setup->getFkName(
                            $tableName,
                            'quote_id',
                            'quote',
                            'entity_id'
                        ),
                        'quote_id',
                        $setup->getTable('quote'),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                ;

                $conn->createTable($quoteShippingTable);
            }
        }

        if (version_compare($version, '1.0.17', '<')) {
            $conn = $setup->getConnection();

            $table = $conn->getTableName('omnyfy_vendor_eav_attribute');
            if ($setup->tableExists($table)) {
                $toHide = [
                    'vendor_id',
                    'priority',
                    'location_name',
                    'description',
                    'address',
                    'suburb',
                    'region',
                    'country',
                    'postcode',
                    'status',
                    'region_id',
                    'booking_lead_time',
                    'timezone',
                    'is_warehouse',
                    'rad_lon',
                    'rad_lat',
                    'cos_lat',
                    'sin_lat',
                    'lon',
                    'lat',
                    'opening_hours',
                    'holiday_hours',
                    'latitude',
                    'longitude',
                ];

                $eavAttrTable = $conn->getTableName('eav_attribute');
                $locationEntityTypeId = $this->getEntityTypeId(\Omnyfy\Vendor\Model\Location::ENTITY, $conn);
                foreach($toHide as $code) {
                    $attributeId = $this->getAttributeId($code, $locationEntityTypeId, $eavAttrTable, $conn);
                    if (empty($attributeId)) {
                        continue;
                    }

                    $this->updateAttribute($attributeId, 'is_visible', 0, $table, $conn);
                }

                $toHideVendor = [
                    'name',
                    'status',
                    'email',
                    'abn',
                    'subscription_status',
                    'subscription_start',
                    'subscription_end',
                ];

                $vendorEntityTypeId = $this->getEntityTypeId(\Omnyfy\Vendor\Model\Vendor::ENTITY, $conn);
                foreach($toHideVendor as $code) {
                    $attributeId = $this->getAttributeId($code, $vendorEntityTypeId, $eavAttrTable, $conn);
                    if (empty($attributeId)) {
                        continue;
                    }

                    $this->updateAttribute($attributeId, 'is_visible', 0, $table, $conn);
                }
            }
        }

        if (version_compare($version, '1.0.18', '<')) {

            // Create the entity gallery
            $table = $setup->getConnection()
                ->newTable($setup->getTable('omnyfy_vendor_entity_gallery'))
                ->addColumn(
                    'value_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true],
                    'Value ID'
                )
                ->addColumn(
                    'attribute_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Attribute ID'
                )
                ->addColumn(
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Store ID'
                )
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Entity ID'
                )
                ->addColumn(
                    'position',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Position'
                )
                ->addColumn(
                    'value',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true, 'default' => null],
                    'Value'
                )
                ->addIndex(
                    $setup->getIdxName(
                        'omnyfy_vendor_entity_gallery',
                        ['entity_id', 'attribute_id', 'store_id'],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['entity_id', 'attribute_id', 'store_id'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->addIndex(
                    $setup->getIdxName('omnyfy_vendor_entity_gallery', ['entity_id']),
                    ['entity_id']
                )
                ->addIndex(
                    $setup->getIdxName('omnyfy_vendor_entity_gallery', ['attribute_id']),
                    ['attribute_id']
                )
                ->addIndex(
                    $setup->getIdxName('omnyfy_vendor_entity_gallery', ['store_id']),
                    ['store_id']
                )
                ->addForeignKey(
                    $setup->getFkName(
                        'omnyfy_vendor_entity_gallery',
                        'attribute_id',
                        'eav_attribute',
                        'attribute_id'
                    ),
                    'attribute_id',
                    $setup->getTable('eav_attribute'),
                    'attribute_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $setup->getFkName(
                        'omnyfy_vendor_entity_gallery',
                        'entity_id',
                        'omnyfy_vendor_entity',
                        'entity_id'
                    ),
                    'entity_id',
                    $setup->getTable('omnyfy_vendor_entity'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $setup->getFkName('omnyfy_vendor_entity_gallery', 'store_id', 'store', 'store_id'),
                    'store_id',
                    $setup->getTable('store'),
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('Omnyfy Vendor Gallery Attribute Backend Table');
            $setup->getConnection()->createTable($table);

            /**
             * Create table 'omnyfy_vendor_entity_media_gallery'
             */
            $table = $setup->getConnection()
                ->newTable(
                    $setup->getTable('omnyfy_vendor_entity_media_gallery')
                )
                ->addColumn(
                    'value_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Value ID'
                )
                ->addColumn(
                    'attribute_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Attribute ID'
                )
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Entity ID'
                )
                ->addColumn(
                    'value',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Value'
                )
                ->addIndex(
                    $setup->getIdxName('omnyfy_vendor_entity_media_gallery', ['attribute_id']),
                    ['attribute_id']
                )
                ->addIndex(
                    $setup->getIdxName('omnyfy_vendor_entity_media_gallery', ['entity_id']),
                    ['entity_id']
                )
                ->addForeignKey(
                    $setup->getFkName(
                        'omnyfy_vendor_entity_media_gallery',
                        'attribute_id',
                        'eav_attribute',
                        'attribute_id'
                    ),
                    'attribute_id',
                    $setup->getTable('eav_attribute'),
                    'attribute_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $setup->getFkName(
                        'omnyfy_vendor_entity_media_gallery',
                        'entity_id',
                        'omnyfy_vendor_entity',
                        'entity_id'
                    ),
                    'entity_id',
                    $setup->getTable('omnyfy_vendor_entity'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment(
                    'Omnyfy Vendor Media Gallery Attribute Backend Table'
                );
            $setup->getConnection()
                ->createTable($table);

            /**
             * Create table 'omnyfy_vendor_entity_media_gallery_value'
             */
            $table = $setup->getConnection()
                ->newTable(
                    $setup->getTable('omnyfy_vendor_entity_media_gallery_value')
                )
                ->addColumn(
                    'value_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
                    'Value ID'
                )
                ->addColumn(
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
                    'Store ID'
                )
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Entity ID'
                )
                ->addColumn(
                    'label',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [],
                    'Label'
                )
                ->addColumn(
                    'position',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true],
                    'Position'
                )
                ->addColumn(
                    'disabled',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Is Disabled'
                )
                ->addIndex(
                    $setup->getIdxName('omnyfy_vendor_entity_media_gallery_value', ['store_id']),
                    ['store_id']
                )
                ->addIndex(
                    $setup->getIdxName('omnyfy_vendor_entity_media_gallery_value', ['entity_id']),
                    ['entity_id']
                )
                ->addForeignKey(
                    $setup->getFkName(
                        'omnyfy_vendor_entity_media_gallery_value',
                        'value_id',
                        'omnyfy_vendor_entity_media_gallery',
                        'value_id'
                    ),
                    'value_id',
                    $setup->getTable('omnyfy_vendor_entity_media_gallery'),
                    'value_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $setup->getFkName('omnyfy_vendor_entity_media_gallery_value', 'store_id', 'store', 'store_id'),
                    'store_id',
                    $setup->getTable('store'),
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $setup->getFkName(
                        'omnyfy_vendor_entity_media_gallery_value',
                        'entity_id',
                        'omnyfy_vendor_vendor_entity',
                        'entity_id'
                    ),
                    'entity_id',
                    $setup->getTable('omnyfy__vendor_vendor_entity'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment(
                    'Omnyfy Vendor Media Gallery Attribute Value Table'
                );
            $setup->getConnection()
                ->createTable($table);

            /**
             * Create table 'omnyfy_vendor_entity_media_gallery_value_to_entity'
             */
            $table = $setup->getConnection()
                ->newTable($setup->getTable(Gallery::GALLERY_VALUE_TO_ENTITY_TABLE))
                ->addColumn(
                    'value_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Value media Entry ID'
                )
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Omnyfy eav attribute id'
                )
                ->addIndex(
                    $setup->getIdxName(
                        Gallery::GALLERY_VALUE_TO_ENTITY_TABLE,
                        ['value_id', 'entity_id'],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['value_id', 'entity_id'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->addForeignKey(
                    $setup->getFkName(
                        Gallery::GALLERY_VALUE_TO_ENTITY_TABLE,
                        'value_id',
                        Gallery::GALLERY_TABLE,
                        'value_id'
                    ),
                    'value_id',
                    $setup->getTable(Gallery::GALLERY_TABLE),
                    'value_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $setup->getFkName(
                        Gallery::GALLERY_VALUE_TO_ENTITY_TABLE,
                        'entity_id',
                        'omnyfy_vendor_vendor_entity',
                        'entity_id'
                    ),
                    'entity_id',
                    $setup->getTable('omnyfy_vendor_vendor_entity'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('Link Media value to Omnyfy Vendor eav attribute table');
            $setup->getConnection()->createTable($table);

            /**
             * Create table 'omnyfy_vendor_entity_media_gallery_value_video'
             */
            $table = $setup->getConnection()
                ->newTable($setup->getTable(self::GALLERY_VALUE_VIDEO_TABLE))
                ->addColumn(
                    'value_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Media Entity ID'
                )
                ->addColumn(
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Store ID'
                )
                ->addColumn(
                    'provider',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    32,
                    ['nullable' => true, 'default' => null],
                    'Video provider ID'
                )
                ->addColumn(
                    'url',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Video URL'
                )
                ->addColumn(
                    'title',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true, 'default' => null],
                    'Title'
                )
                ->addColumn(
                    'description',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Page Meta Description'
                )
                ->addColumn(
                    'metadata',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Video meta data'
                )
                ->addIndex(
                    $setup->getIdxName(
                        self::GALLERY_VALUE_VIDEO_TABLE,
                        ['value_id', 'store_id'],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['value_id', 'store_id'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->addForeignKey(
                    $setup->getFkName(
                        self::GALLERY_VALUE_VIDEO_TABLE,
                        'value_id',
                        Gallery::GALLERY_TABLE,
                        'value_id'
                    ),
                    'value_id',
                    $setup->getTable(Gallery::GALLERY_TABLE),
                    'value_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $setup->getFkName(
                        self::GALLERY_VALUE_VIDEO_TABLE,
                        'store_id',
                        $setup->getTable('store'),
                        'store_id'
                    ),
                    'store_id',
                    $setup->getTable('store'),
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('Omnyfy Vendor Video Table');

            $setup->getConnection()->createTable($table);
        }

        if (version_compare($version, '1.0.19', '<')) {
            $conn = $setup->getConnection();

            $tableName = $conn->getTableName('omnyfy_vendor_customer_favourites');
            if (!$setup->tableExists($tableName)) {
                $customerVendorTable = $setup->getConnection()->newTable(
                    $tableName
                )
                    ->addColumn(
                        'id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                        'SEQ ID'
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
                        $setup->getIdxName(
                            $tableName,
                            ['customer_id'],
                            AdapterInterface::INDEX_TYPE_INDEX
                        ),
                        ['customer_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_INDEX]
                    )
                    ->addForeignKey(
                        $setup->getFkName(
                            $tableName,
                            'customer_id',
                            'customer_entity',
                            'entity_id'
                        ),
                        'customer_id',
                        $setup->getTable('customer_entity'),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName(
                            $tableName,
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
                $setup->getConnection()->createTable($customerVendorTable);
            }
        }

        if (version_compare($version, '1.0.20', '<')) {
            $conn = $setup->getConnection();

            //add region_id column into quote_item table
            $quoteItemsTable = $setup->getConnection()->getTableName('quote_item');
            if ($setup->tableExists($quoteItemsTable) && !$setup->getConnection()->tableColumnExists($quoteItemsTable, 'kitstore_id')) {
                $setup->getConnection()->addColumn(
                    $quoteItemsTable,
                    'kitstore_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Kitstore Page Id',
                        'unsigned' => true,
                        'default' => '0'
                    ]
                );
            }

            //add kitstore_id column into sales_order_item table
            $salesOrderItemTable = $setup->getConnection()->getTableName('sales_order_item');
            if ($setup->tableExists($salesOrderItemTable) && !$setup->getConnection()->tableColumnExists($salesOrderItemTable, 'kitstore_id')) {
                $setup->getConnection()->addColumn(
                    $salesOrderItemTable,
                    'kitstore_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Kitstore Page Id',
                        'unsigned' => true,
                        'default' => '0'
                    ]
                );
            }
        }

        if (version_compare($version, '1.0.21', '<')) {
            $tableName = $setup->getConnection()->getTableName('omnyfy_vendor_eav_attribute');
            if ($setup->tableExists($tableName) && !$setup->getConnection()->tableColumnExists($tableName, 'tooltip')) {
                $setup->getConnection()->addColumn(
                    $tableName,
                    'tooltip',
                    [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Tooltip',
                        'length' => 255
                    ]
                );
            }
        }

        if (version_compare($version, '1.0.22', '<')) {
            $conn = $setup->getConnection();
            $tableName = $conn->getTableName('omnyfy_vendor_vendor_type');
            $vendorDefaultAttributeSetId = $this->getEntityDefaultAttributeSetId(
                \Omnyfy\Vendor\Model\Vendor::ENTITY,
                $conn
            );

            $locationDefaultAttributeSetId = $this->getEntityDefaultAttributeSetId(
                \Omnyfy\Vendor\Model\Location::ENTITY,
                $conn
            );
            $conn->update($tableName,
                [
                    'vendor_attribute_set_id' => $vendorDefaultAttributeSetId,
                    'location_attribute_set_id' => $locationDefaultAttributeSetId
                ],
                'type_id=1'
            );
        }

        if (version_compare($version, '1.0.23', '<')) {
            $tableName = $setup->getConnection()->getTableName('omnyfy_vendor_vendor_user_stores');
            if (!$setup->tableExists($tableName)) {
                $adminUserVendorTable = $setup->getConnection()->newTable(
                    $setup->getTable('omnyfy_vendor_vendor_user_stores')
                )
                    ->addColumn(
                        'user_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true],
                        'Admin User ID'
                    )
                    ->addColumn(
                        'store_id',
                        Table::TYPE_TEXT,
                        '64k',
                        ['nullable' => true],
                        'User Stores'
                    )
                    ->addIndex(
                        $setup->getIdxName(
                            'omnyfy_vendor_vendor_user_store',
                            ['user_id'],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        ['user_id'],
                        ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                    )
                ;
                $setup->getConnection()->createTable($adminUserVendorTable);
                $tableName = $setup->getTable('omnyfy_vendor_vendor_user_stores');
                if ($setup->tableExists($tableName)) {
                    $setup->getConnection()->addForeignKey(
                        $setup->getFkName(
                            $tableName,
                            'user_id',
                            $setup->getTable('admin_user'),
                            'user_id'
                        ),
                        $tableName,
                        'user_id',
                        $setup->getTable('admin_user'),
                        'user_id'
                    );
                }
            }
        }

        if (version_compare($version, '1.0.25', '<')) {
            $conn = $setup->getConnection();

            $vendorShippingTable = $setup->getConnection()->getTableName('omnyfy_vendor_quote_shipping');
            if ($setup->tableExists($vendorShippingTable) && !$setup->getConnection()->tableColumnExists($vendorShippingTable, 'vendor_id')) {
                $setup->getConnection()->addColumn(
                    $vendorShippingTable,
                    'vendor_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Vendor Id',
                        'unsigned' => true,
                        'default' => '0'
                    ]
                );
            }
        }

        if(version_compare($version, '1.0.28', '<')){
            $conn = $setup->getConnection();
            $vendorLocationTable = $setup->getConnection()->getTableName('omnyfy_vendor_location_entity');
            if ($setup->tableExists($vendorLocationTable)) {
                if (!$setup->getConnection()->tableColumnExists($vendorLocationTable, 'location_contact_name')) {
                    $setup->getConnection()->addColumn(
                        $vendorLocationTable,
                        'location_contact_name',
                        [
                            'type' => Table::TYPE_TEXT,
                            'nullable' => false,
                            'comment' => 'Location Contact Name',
                            'length' => 255
                        ]
                    );
                }
                if (!$setup->getConnection()->tableColumnExists($vendorLocationTable, 'location_contact_phone')) {
                    $setup->getConnection()->addColumn(
                        $vendorLocationTable,
                        'location_contact_phone',
                        [
                            'type' => Table::TYPE_TEXT,
                            'nullable' => false,
                            'comment' => 'Location Contact Phone',
                            'length' => 255
                        ]
                    );
                }
                if (!$setup->getConnection()->tableColumnExists($vendorLocationTable, 'location_contact_email')) {
                    $setup->getConnection()->addColumn(
                        $vendorLocationTable,
                        'location_contact_email',
                        [
                            'type' => Table::TYPE_TEXT,
                            'nullable' => false,
                            'comment' => 'Location Contact Email',
                            'length' => 255
                        ]
                    );
                }
                if (!$setup->getConnection()->tableColumnExists($vendorLocationTable, 'location_company_name')) {
                    $setup->getConnection()->addColumn(
                        $vendorLocationTable,
                        'location_company_name',
                        [
                            'type' => Table::TYPE_TEXT,
                            'nullable' => false,
                            'comment' => 'Location Company Name',
                            'length' => 255
                        ]
                    );
                }
            }
        }

        $setup->endSetup();
    }

    private function getEntityTypeId($entityTypeCode, $conn)
    {
        $table = $conn->getTableName('eav_entity_type');
        $select = $conn->select()
            ->from($table, ['entity_type_id'])
            ->where('entity_type_code=?', $entityTypeCode);

        return $conn->fetchOne($select);
    }

    private function getEntityDefaultAttributeSetId($entityTypeCode, $conn)
    {
        $table = $conn->getTableName('eav_entity_type');
        $select = $conn->select()
            ->from($table, ['default_attribute_set_id'])
            ->where('entity_type_code=?', $entityTypeCode);

        return $conn->fetchOne($select);
    }

    private function getAttributeId($attributeCode, $typeId, $table, $conn)
    {
        $select = $conn->select()
            ->from($table, ['attribute_id'])
            ->where('entity_type_id=?', $typeId)
            ->where('attribute_code=?', $attributeCode);

        return $conn->fetchOne($select);
    }

    private function updateAttribute($attributeId, $column, $value, $table, $conn)
    {
        $conn->update(
            $table,
            [$column => $value],
            ['attribute_id=?' => $attributeId]
        );
    }

    private function deleteAttribute($attributeId, $table, $conn)
    {
        $conn->delete($table, ['attribute_id=?' => $attributeId]);
    }
}
