<?php

namespace Omnyfy\Postcode\Setup;

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{

    /**
     * Upgrade data for module
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     */
    public function upgrade(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();

        $connection = $installer->getConnection();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $regions = [
                [
                    'country_id'    => 'AU',
                    'code'          => 'ACT',
                    'default_name'  => 'Australian Capital Territory',
                    'timezone'      => 'Australia/Sydney'
                ],
                [
                    'country_id'    => 'AU',
                    'code'          => 'NSW',
                    'default_name'  => 'New South Wales',
                    'timezone'      => 'Australia/Sydney'
                ],
                [
                    'country_id'    => 'AU',
                    'code'          => 'NT',
                    'default_name'  => 'Northern Territory',
                    'timezone'      => 'Australia/Darwin'
                ],
                [
                    'country_id'    => 'AU',
                    'code'          => 'QLD',
                    'default_name'  => 'Queensland',
                    'timezone'      => 'Australia/Brisbane'
                ],
                [
                    'country_id'    => 'AU',
                    'code'          => 'SA',
                    'default_name'  => 'South Australia',
                    'timezone'      => 'Australia/Adelaide'
                ],
                [
                    'country_id'    => 'AU',
                    'code'          => 'TAS',
                    'default_name'  => 'Tasmania',
                    'timezone'      => 'Australia/Hobart'
                ],
                [
                    'country_id'    => 'AU',
                    'code'          => 'VIC',
                    'default_name'  => 'Victoria',
                    'timezone'      => 'Australia/Melbourne'
                ],
                [
                    'country_id'    => 'AU',
                    'code'          => 'WA',
                    'default_name'  => 'Western Australia',
                    'timezone'      => 'Australia/Perth'
                ],
            ];

            $tableName = $installer->getTable('directory_country_region');
            if ($connection->isTableExists($tableName)) {
                $connection->delete($tableName, "country_id = 'AU'");

                foreach ($regions as $region) {
                    $connection->insert($tableName, $region);
                }
            }
        }

        $installer->endSetup();
    }

}