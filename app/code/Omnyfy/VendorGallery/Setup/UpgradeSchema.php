<?php
namespace Omnyfy\VendorGallery\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements  UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $mcmVendorGalleryItemTable = 'omnyfy_vendor_gallery_item';

        $version = $context->getVersion();
        $connection = $setup->getConnection();

        if (version_compare($version, '1.0.1') < 0) {
            $connection->addColumn(
                $setup->getTable($mcmVendorGalleryItemTable), 'video_title', [
                    'type' => Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => true,
                    'comment' => 'Video Title',
                ]
            );
            $connection->addColumn(
                $setup->getTable($mcmVendorGalleryItemTable), 'video_description', [
                    'type' => Table::TYPE_TEXT,
                    'length' => '64k',
                    'nullable' => true,
                    'comment' => 'Video Description',
                ]
            );
            $connection->addColumn(
                $setup->getTable($mcmVendorGalleryItemTable), 'video_metadata', [
                    'type' => Table::TYPE_TEXT,
                    'length' => '64k',
                    'nullable' => true,
                    'comment' => 'Video Metadata',
                ]
            );
        }

        if (version_compare($version, '1.0.2') < 0) {
            $connection->addColumn(
                $setup->getTable($mcmVendorGalleryItemTable), 'caption', [
                    'type' => Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => true,
                    'comment' => 'Item Caption',
                ]
            );
        }
        $installer->endSetup();
    }
}
