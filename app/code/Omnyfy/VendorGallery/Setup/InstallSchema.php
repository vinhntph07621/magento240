<?php
namespace Omnyfy\VendorGallery\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface {

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();
        /**
         * Create table 'omnyfy_vendor_gallery_album'
         */
        $table = $installer->getConnection()
                ->newTable($installer->getTable('omnyfy_vendor_gallery_album'))
                ->addColumn(
                        'entity_id', Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Album ID'
                )->addColumn(
                        'vendor_id', Table::TYPE_INTEGER, null, ['nullable' => false], 'Vendor ID'
                )->addColumn(
                        'name', Table::TYPE_TEXT, '255', ['nullable' => false], 'Album Name'
                )->addColumn(
                        'description', Table::TYPE_TEXT, '255', ['nullable' => false], 'Album Description'
                )->addColumn(
                        'status', Table::TYPE_SMALLINT, '1', ['nullable' => false, 'default' => 1], 'Album Status'
                )->addColumn(
                        'created_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false], 'Creation time'
                )->addColumn(
                        'updated_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false], 'Last Modification Time'
                )->setComment(
                'Omnyfy Marketplace Vendor Gallery Albums'
        );
        $installer->getConnection()->createTable($table);
        /**
         * Create table 'omnyfy_vendor_gallery_item'
         */
        $table = $installer->getConnection()
                ->newTable($installer->getTable('omnyfy_vendor_gallery_item'))
                ->addColumn(
                        'entity_id', Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Item ID'
                )->addColumn(
                        'album_id', Table::TYPE_INTEGER, null, ['nullable' => false], 'Album ID'
                )->addColumn(
                        'type', Table::TYPE_SMALLINT, '1', ['nullable' => false, 'default' => 1], 'Item Type'
                )->addColumn(
                        'status', Table::TYPE_SMALLINT, '1', ['nullable' => false, 'default' => 1], 'Item Status'
                )->addColumn(
                        'url', Table::TYPE_TEXT, '255', ['nullable' => false], 'Item Url'
                )->addColumn(
                        'preview_image', Table::TYPE_TEXT, '255', ['nullable' => true], 'Preview Image for Item type video'
                )->addColumn(
                        'is_thumbnail', Table::TYPE_SMALLINT, '1', ['nullable' => false, 'default' => 0], 'Is items a thumbnail'
                )->addColumn(
                        'position', Table::TYPE_INTEGER, null, ['nullable' => false], 'Item position in album'
                )->setComment(
                'Omnyfy Marketplace Vendor Gallery Items'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'omnyfy_vendor_gallery_album_location'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('omnyfy_vendor_gallery_album_location'))
            ->addColumn(
                'entity_id', Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Item ID'
            )->addColumn(
                'album_id', Table::TYPE_INTEGER, null, ['nullable' => false], 'Album ID'
            )->addColumn(
                'location_id', Table::TYPE_INTEGER, null, ['nullable' => false], 'Location ID'
            )->setComment(
                'Omnyfy Marketplace Vendor Gallery Link album and location'
            );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
