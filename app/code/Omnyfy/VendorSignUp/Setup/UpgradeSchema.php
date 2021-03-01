<?php
/**
 * Project: Vendor Signup
 * User: jing
 * Date: 2019-07-09
 * Time: 13:36
 */
namespace Omnyfy\VendorSignUp\Setup;

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
        if (version_compare($version, '1.0.1' , '<')) {
            $signupTable = $conn->getTableName('omnyfy_vendor_signup');
            if ($setup->tableExists($signupTable)) {
                if (!$conn->tableColumnExists($signupTable, 'vendor_type_id')) {
                    $conn->addColumn(
                        $signupTable,
                        'vendor_type_id',
                        [
                            'type' => Table::TYPE_INTEGER,
                            'nullable' => true,
                            'comment' => 'Vendor Type ID',
                            'unsigned' => true,
                            'default' => null,
                        ]
                    );
                }

                if (!$conn->tableColumnExists($signupTable, 'extra_info')) {
                    $conn->addColumn(
                        $signupTable,
                        'extra_info',
                        [
                            'type' => Table::TYPE_TEXT,
                            'size' => 1024,
                            'nullable' => true,
                            'comment' => 'Extra Information',
                            'default' => null,
                        ]
                    );
                }

                if (!$conn->tableColumnExists($signupTable, 'email_sent')) {
                    $conn->addColumn(
                        $signupTable,
                        'email_sent',
                        [
                            'type' => Table::TYPE_SMALLINT,
                            'size' => 5,
                            'nullable' => false,
                            'comment' => 'Flag of email sent',
                            'default' => 0,
                            'after' => 'status'
                        ]
                    );
                }
            }
        }

        if (version_compare($version, '1.0.2' , '<')) {
            $this->addColumnExtensionAttribute($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param $setup SchemaSetupInterface
     */
    private function addColumnExtensionAttribute($setup){
        $connection = $setup->getConnection();
        if (!$connection->tableColumnExists($setup->getTable('omnyfy_vendor_signup'), 'extend_attribute')) {
            $connection->addColumn(
                $setup->getTable('omnyfy_vendor_signup'),
                'extend_attribute',
                [
                    'type' => Table::TYPE_TEXT,
                    'size' => 2048,
                    'nullable' => true,
                    'comment' => 'Extend Attribute'
                ]
            );
        }
    }
}
 