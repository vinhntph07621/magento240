<?php

namespace Omnyfy\Postcode\Setup;

use \Magento\Framework\DB\Ddl\Table;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{

    /**
     * Installs DB schema for a module
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();

        if (!$context->getVersion()) {
            $connection = $installer->getConnection();

            // Drop any existing postcode table
            $tablename = $installer->getTable(\Omnyfy\Postcode\Model\ResourceModel\Postcode::TABLE_NAME);
            if ($installer->tableExists($tablename)) {
                $connection->dropTable($tablename);
            }

            // Create postcode table
            $postcodeTable = $connection->newTable($tablename)
                ->addColumn(
                    'postcode_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                    'Entity ID'
                )
                ->addColumn(
                    'country_id',
                    Table::TYPE_TEXT,
                    2,
                    ['nullable' => false],
                    'Country ID'
                )
                ->addColumn(
                    'postcode',
                    Table::TYPE_TEXT,
                    24,
                    ['nullable' => false],
                    'Postcode'
                )
                ->addColumn(
                    'suburb',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Suburb/City/Town'
                )
                ->addColumn(
                    'latitude',
                    Table::TYPE_FLOAT,
                    null,
                    ['nullable' => false],
                    'Latitude'
                )
                ->addColumn(
                    'longitude',
                    Table::TYPE_FLOAT,
                    null,
                    ['nullable' => false],
                    'Longitude'
                )
                ->addForeignKey(
                    $installer->getFkName($tablename, 'country_id', $installer->getTable('directory_country'), 'country_id'),
                    'country_id',
                    $installer->getTable('directory_country'),
                    'country_id',
                    Table::ACTION_CASCADE
                )
            ;
            $connection->createTable($postcodeTable);
        }

        $installer->endSetup();
    }

}
