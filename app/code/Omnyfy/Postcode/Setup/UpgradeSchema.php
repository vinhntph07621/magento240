<?php

namespace Omnyfy\Postcode\Setup;

use \Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{

    /**
     * Upgrades db schema
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     */
    public function upgrade(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();

        $connection = $installer->getConnection();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $tableName = $installer->getTable(\Omnyfy\Postcode\Model\ResourceModel\Postcode::TABLE_NAME);
            if ($connection->isTableExists($tableName)) {
                $connection->addColumn($tableName, 'region_code', [
                    'type'      => Table::TYPE_TEXT,
                    'length'    => 32,
                    'nullable'  => true,
                    'comment'   => 'Region Code',
                    'after'     => 'country_id'
                ]);

                $connection->addColumn($tableName, 'timezone_override', [
                    'type'      => Table::TYPE_TEXT,
                    'length'    => 100,
                    'nullable'  => true,
                    'comment'   => 'Timezone Override'
                ]);
            }

            $tableName = $installer->getTable('directory_country_region');
            if ($connection->isTableExists($tableName)) {
                $connection->addColumn($tableName, 'timezone', [
                    'type'      => Table::TYPE_TEXT,
                    'length'    => 100,
                    'nullable'  => true,
                    'comment'   => 'Timezone'
                ]);
            }
        }

        $installer->endSetup();
    }

}