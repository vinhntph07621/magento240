<?php

namespace Omnyfy\Mcm\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface {

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();
        /**
         * Create table 'omnyfy_mcm_fees_and_charges'
         */
        $table = $installer->getConnection()
                ->newTable($installer->getTable('omnyfy_mcm_fees_and_charges'))
                ->addColumn(
                        'id', Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Fee and charges ID'
                )->addColumn(
                        'vendor_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false], 'Vendor ID'
                )->addColumn(
                        'seller_fee', Table::TYPE_DECIMAL, '12,2', ['nullable' => false], 'Seller Fee %'
                )->addColumn(
                        'min_seller_fee', Table::TYPE_DECIMAL, '12,2', ['nullable' => true, 'default' => null], 'Minimum Seller Fee'
                )->addColumn(
                        'disbursement_fee', Table::TYPE_DECIMAL, '12,2', ['nullable' => false], 'Disbursement Fee'
                )->addColumn(
                        'created_at', Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'Creation time'
                )->addColumn(
                        'updated_at', Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'Last Modification Time'
                )->addForeignKey(
                        $setup->getFkName('omnyfy_mcm_fees_and_charges', 'vendor_id', 'omnyfy_vendor_vendor_entity', 'entity_id'), 'vendor_id', $setup->getTable('omnyfy_vendor_vendor_entity'), 'entity_id', Table::ACTION_CASCADE
                )
                ->setComment(
                'Omnyfy Marketplace Commercials Management Fees and Charges Table'
        );
        $installer->getConnection()->createTable($table);
        /**
         * Create table 'omnyfy_mcm_vendor_payout'
         */
        $table = $installer->getConnection()
                ->newTable($installer->getTable('omnyfy_mcm_vendor_payout'))
                ->addColumn(
                        'payout_id', Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Payout ID'
                )->addColumn(
                        'fees_charges_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false], 'Fees charges ID'
                )->addColumn(
                        'vendor_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false], 'Vendor ID'
                )->addColumn(
                        'ewallet_id', Table::TYPE_TEXT, '100', ['nullable' => true, 'default' => null], 'Ewallet Id'
                )->addColumn(
                        'account_ref', Table::TYPE_TEXT, '100', ['nullable' => true, 'default' => null], 'Account ref   '
                )->addColumn(
                        'created_at', Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'Creation time'
                )->addColumn(
                        'updated_at', Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'Last Modification Time'
                )->addForeignKey(
                        $setup->getFkName('omnyfy_mcm_vendor_payout', 'vendor_id', 'omnyfy_vendor_vendor_entity', 'entity_id'), 'vendor_id', $setup->getTable('omnyfy_vendor_vendor_entity'), 'entity_id', Table::ACTION_CASCADE
                )
                ->setComment(
                'Omnyfy Marketplace Commercials Management Vendor Payout Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'omnyfy_mcm_vendor_payout_history'
         */
        $table = $installer->getConnection()
                ->newTable($installer->getTable('omnyfy_mcm_vendor_payout_history'))
                ->addColumn(
                        'id', Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Payout history ID'
                )->addColumn(
                        'payout_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false], 'Payout ID'
                )->addColumn(
                        'payout_ref', Table::TYPE_TEXT, 40, ['nullable' => false], 'Payout Reference Number'
                )->addColumn(
                        'status', Table::TYPE_SMALLINT, null, ['nullable' => false], 'Payout Status: 0 = Failed, 1 = Suceess, 2 = In progress'
                )->addColumn(
                        'created_at', Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'Creation time'
                )->addColumn(
                        'updated_at', Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'Last Modification Time'
                )->addForeignKey(
                        $setup->getFkName('omnyfy_mcm_vendor_payout_history', 'payout_id', 'omnyfy_mcm_vendor_payout', 'payout_id'), 'payout_id', $setup->getTable('omnyfy_mcm_vendor_payout'), 'payout_id', Table::ACTION_CASCADE
                )
                ->setComment(
                'Omnyfy Marketplace Commercials Management payout history Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'omnyfy_mcm_vendor_order'
         */
        $table = $installer->getConnection()
                ->newTable($installer->getTable('omnyfy_mcm_vendor_order'))
                ->addColumn(
                        'id', Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'ID'
                )->addColumn(
                        'order_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false], 'Order ID'
                )->addColumn(
                        'vendor_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false], 'Vendor ID'
                )->addColumn(
                        'total_category_fee', Table::TYPE_DECIMAL, '12,2', ['nullable' => true, 'default' => null], 'Total Category Fee'
                )->addColumn(
                        'total_seller_fee', Table::TYPE_DECIMAL, '12,2', ['nullable' => true, 'default' => null], 'Total Seller Fee'
                )->addColumn(
                        'disbursement_fee', Table::TYPE_DECIMAL, '12,2', ['nullable' => true, 'default' => null], 'Disbursement Fee'
                )->addColumn(
                        'disbursement_fee_tax', Table::TYPE_DECIMAL, '10,2', ['nullable' => true, 'default' => null], 'Disbursement Fee Tax'
                )->addColumn(
                        'total_tax_onfees', Table::TYPE_DECIMAL, '10,2', ['nullable' => true, 'default' => null], 'Total Tax on Fees'
                )->addColumn(
                        'payout_amount', Table::TYPE_DECIMAL, '12,2', ['nullable' => true, 'default' => null], 'Transaction Fee Tax'
                )->addColumn(
                        'payout_status', Table::TYPE_SMALLINT, null, ['nullable' => false], 'Vendor Payout Status: 0 = Unpaid, 1 = Paid, 2 = refund, 3 = In progress'
                )->addColumn(
                        'payout_action', Table::TYPE_SMALLINT, null, ['nullable' => false], 'Order Payout Action: 0 = Pending, 1 = Added to payout, 2 = Refunded'
                )->addForeignKey(
                        $setup->getFkName('omnyfy_mcm_vendor_order', 'order_id', 'sales_order', 'entity_id'), 'order_id', $setup->getTable('sales_order'), 'entity_id', Table::ACTION_CASCADE
                )->addForeignKey(
                        $setup->getFkName('omnyfy_mcm_vendor_order', 'vendor_id', 'omnyfy_vendor_vendor_entity', 'entity_id'), 'vendor_id', $setup->getTable('omnyfy_vendor_vendor_entity'), 'entity_id', Table::ACTION_CASCADE
                )->setComment(
                'Omnyfy Marketplace Commercials Management vendor order Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'omnyfy_mcm_vendor_order_item'
         */
        $table = $installer->getConnection()
                ->newTable($installer->getTable('omnyfy_mcm_vendor_order_item'))
                ->addColumn(
                        'id', Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'ID'
                )->addColumn(
                        'vendor_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false], 'Vendor ID'
                )->addColumn(
                        'order_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false], 'Order ID'
                )->addColumn(
                        'order_item_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false], 'order item id'
                )->addColumn(
                        'seller_fee', Table::TYPE_DECIMAL, '12,2', ['nullable' => true, 'default' => null], 'Seller Fee'
                )->addColumn(
                        'seller_fee_tax', Table::TYPE_DECIMAL, '10,2', ['nullable' => true, 'default' => null], 'Seller Fee Tax'
                )->addColumn(
                        'category_fee', Table::TYPE_DECIMAL, '12,2', ['nullable' => true, 'default' => null], 'Category Fee'
                )->addColumn(
                        'category_fee_tax', Table::TYPE_DECIMAL, '10,2', ['nullable' => true, 'default' => null], 'Category Fee Tax'
                )->addColumn(
                        'row_total', Table::TYPE_DECIMAL, '10,2', ['nullable' => true, 'default' => null], 'Item row total'
                )->addColumn(
                        'tax_amount', Table::TYPE_DECIMAL, '10,2', ['nullable' => true, 'default' => null], 'Tax Amount'
                )->addColumn(
                        'row_total_incl_tax', Table::TYPE_DECIMAL, '10,2', ['nullable' => true, 'default' => null], 'Item Row Total Incl. Tax'
                )->addForeignKey(
                        $setup->getFkName('omnyfy_mcm_vendor_order_item', 'vendor_id', 'omnyfy_vendor_vendor_entity', 'entity_id'), 'vendor_id', $setup->getTable('omnyfy_vendor_vendor_entity'), 'entity_id', Table::ACTION_CASCADE
                )->addForeignKey(
                        $setup->getFkName('omnyfy_mcm_vendor_order_item', 'order_id', 'sales_order', 'entity_id'), 'order_id', $setup->getTable('sales_order'), 'entity_id', Table::ACTION_CASCADE
                )
                ->addForeignKey(
                        $setup->getFkName('omnyfy_mcm_vendor_order_item', 'order_item_id', 'sales_order_item', 'item_id'), 'order_item_id', $setup->getTable('sales_order_item'), 'item_id', Table::ACTION_CASCADE
                )
                ->setComment(
                'Omnyfy Marketplace Commercials Management order Item Table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }

}
