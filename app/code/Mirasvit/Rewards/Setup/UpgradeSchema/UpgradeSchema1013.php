<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rewards
 * @version   3.0.21
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Rewards\Setup\UpgradeSchema;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema1013 implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public static function upgrade(SchemaSetupInterface $installer, ModuleContextInterface $context)
    {
        $installer->getConnection()->dropForeignKey(
            $installer->getTable('mst_rewards_notification_rule_website'),
            $installer->getFkName(
                'mst_rewards_notification_rule_website',
                'website_id',
                'store',
                'store_id'
            )
        );
        $installer->getConnection()->addForeignKey(
            $installer->getFkName(
                'mst_rewards_notification_rule_website',
                'website_id',
                'store_website',
                'website_id'
            ),
            $installer->getTable('mst_rewards_notification_rule_website'),
            'website_id',
            $installer->getTable('store_website'),
            'website_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $installer->getConnection()->dropForeignKey(
            $installer->getTable('mst_rewards_earning_rule_website'),
            $installer->getFkName(
                'mst_rewards_earning_rule_website',
                'website_id',
                'store',
                'store_id'
            )
        );
        $installer->getConnection()->addForeignKey(
            $installer->getFkName(
                'mst_rewards_earning_rule_website',
                'website_id',
                'store_website',
                'website_id'
            ),
            $installer->getTable('mst_rewards_earning_rule_website'),
            'website_id',
            $installer->getTable('store_website'),
            'website_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $installer->getConnection()->dropForeignKey(
            $installer->getTable('mst_rewards_spending_rule_website'),
            $installer->getFkName(
                'mst_rewards_spending_rule_website',
                'website_id',
                'store',
                'store_id'
            )
        );
        $installer->getConnection()->addForeignKey(
            $installer->getFkName(
                'mst_rewards_spending_rule_website',
                'website_id',
                'store_website',
                'website_id'
            ),
            $installer->getTable('mst_rewards_spending_rule_website'),
            'website_id',
            $installer->getTable('store_website'),
            'website_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
    }
}