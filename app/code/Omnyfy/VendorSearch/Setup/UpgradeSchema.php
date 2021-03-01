<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 4/9/17
 * Time: 10:45 AM
 */

namespace Omnyfy\VendorSearch\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Omnyfy\Vendor\Model\Resource\Vendor\Gallery;

class UpgradeSchema implements UpgradeSchemaInterface
{

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $version = $context->getVersion();

        if (version_compare($version, '1.0.3', '<')) {
            $locationTable = $setup->getConnection()->getTableName('omnyfy_vendor_vendor_type');
            if ($setup->tableExists($locationTable) && !$setup->getConnection()->tableColumnExists($locationTable, 'location_type')) {
                $setup->getConnection()->addColumn(
                    'omnyfy_vendor_vendor_type',
                    'location_type',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'Location Type',
                        'unsigned' => true,
                        'default' => '1'
                    ]
                );
            }
        }

        $setup->endSetup();
    }
}
