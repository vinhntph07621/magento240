<?php

namespace Omnyfy\Postcode\Setup;

use \Magento\Framework\Setup\InstallDataInterface;

class InstallData implements InstallDataInterface
{

    /**
     * Install data for module
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     */
    public function install(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();

        if (!$context->getVersion()) {
            $connection = $installer->getConnection();
            $connection->insert($installer->getTable('eav_entity_type'), [
                'entity_type_code'  => \Omnyfy\Postcode\Model\Postcode::ENTITY,
                'entity_model'      => 'Omnyfy\Postcode\Model\ResourceModel\Postcode',
                'entity_table'      => \Omnyfy\Postcode\Model\ResourceModel\Postcode::TABLE_NAME,
                'is_data_sharing'   => '1',
                'data_sharing_key'  => 'default',
            ]);
        }

        $installer->endSetup();
    }

}