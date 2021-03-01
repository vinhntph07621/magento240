<?php


namespace Omnyfy\VendorFeatured\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    )
    {
        $setup->startSetup();
        $connection = $setup->getConnection();
        if (version_compare($context->getVersion(), "1.0.1", "<")) {
            $enquiryTable = $setup->getTable('omnyfy_vendorfeatured_vendor_featured');
            if ($setup->tableExists($enquiryTable) && !$connection->tableColumnExists($enquiryTable, 'location_id')) {
                $connection->addColumn(
                    $enquiryTable,
                    'location_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Location ID',
                        'unsigned' => true,
                    ]
                );
            }
        }
    }
}
