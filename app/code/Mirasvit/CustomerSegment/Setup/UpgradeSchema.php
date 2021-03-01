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
 * @package   mirasvit/module-customer-segment
 * @version   1.0.51
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\CustomerSegment\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Mirasvit\CustomerSegment\Api\Data\Segment\CustomerInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $this->addIndexToSegmentCustomerTable($setup);
            $this->addIndexToOrderTable($setup);
        }

        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $this->addAddressToSegmentCustomerTable($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addIndexToSegmentCustomerTable(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addIndex(
            $setup->getTable(CustomerInterface::TABLE_NAME),
            $setup->getIdxName(
                CustomerInterface::TABLE_NAME,
                CustomerInterface::EMAIL,
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            CustomerInterface::EMAIL,
            AdapterInterface::INDEX_TYPE_INDEX
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addIndexToOrderTable(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addIndex(
            $setup->getTable('sales_order'),
            $setup->getIdxName(
                'sales_order',
                OrderInterface::CUSTOMER_EMAIL,
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            OrderInterface::CUSTOMER_EMAIL,
            AdapterInterface::INDEX_TYPE_INDEX
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addAddressToSegmentCustomerTable(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable(CustomerInterface::TABLE_NAME),
            CustomerInterface::BILLING_ADDRESS_ID,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => true,
                'after'    => CustomerInterface::EMAIL,
                'comment'  => 'Order Billing Address ID'
            ]
        );

        $setup->getConnection()->addIndex(
            $setup->getTable(CustomerInterface::TABLE_NAME),
            $setup->getIdxName(
                CustomerInterface::TABLE_NAME,
                CustomerInterface::BILLING_ADDRESS_ID,
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            CustomerInterface::BILLING_ADDRESS_ID,
            AdapterInterface::INDEX_TYPE_INDEX
        );
    }
}
