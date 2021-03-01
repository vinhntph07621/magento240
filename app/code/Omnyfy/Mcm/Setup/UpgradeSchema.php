<?php

/* tables 
 * omnyfy_mcm_vendor_bank_account_type,  
 * omnyfy_mcm_vendor_bank_account, 
 * omnyfy_mcm_vendor_bank_withdrawals_history,
 * omnyfy_mcm_vendor_invoice, 
 * omnyfy_mcm_vendor_invoice,
 * omnyfy_mcm_vendor_invoice_item 
 */

namespace Omnyfy\Mcm\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface {

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();

        $quoteTable = 'quote';
        $quoteAddressTable = 'quote_address';
        $orderTable = 'sales_order';
        $invoiceTable = 'sales_invoice';
        $creditmemoTable = 'sales_creditmemo';
        $mcmVendorOrderTable = 'omnyfy_mcm_vendor_order';
        $vendorPayoutHistory = 'omnyfy_mcm_vendor_payout_history';
        $omnyfyMcmVendorOrderItem = 'omnyfy_mcm_vendor_order_item';

        $version = $context->getVersion();
        $connection = $setup->getConnection();

        if (version_compare($version, '1.0.1', '<')) {
            if (!$installer->tableExists('omnyfy_mcm_vendor_bank_account_type')) {
                $bank_account_type_Table = $installer->getConnection()->newTable(
                                $installer->getTable('omnyfy_mcm_vendor_bank_account_type')
                        )
                        ->addColumn(
                                'id', Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'ID'
                        )
                        ->addColumn(
                        'account_type', Table::TYPE_TEXT, 255, ['nullable' => false], 'Account Type'
                );
                $installer->getConnection()->createTable($bank_account_type_Table);
            }
            if (!$installer->tableExists('omnyfy_mcm_vendor_bank_account')) {
                $bank_account_Table = $installer->getConnection()->newTable(
                                $installer->getTable('omnyfy_mcm_vendor_bank_account')
                        )
                        ->addColumn(
                                'id', Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'ID'
                        )
                        ->addColumn(
                                'vendor_id', Table::TYPE_INTEGER, null, ['nullable' => false, 'default' => 0, 'unsigned' => true], 'Vendor ID'
                        )
                        ->addColumn(
                                'bank_name', Table::TYPE_TEXT, 80, ['nullable' => false, 'default' => ''], 'Bank Name'
                        )
                        ->addColumn(
                                'account_type_id', Table::TYPE_INTEGER, null, ['nullable' => false, 'default' => 0, 'unsigned' => true], 'Account Type ID'
                        )
                        ->addColumn(
                                'account_name', Table::TYPE_TEXT, 100, ['nullable' => false, 'default' => ''], 'Account Name'
                        )
                        ->addColumn(
                                'account_number', Table::TYPE_TEXT, 100, ['nullable' => false, 'default' => ''], 'Account Type'
                        )
                        ->addColumn(
                                'bsb', Table::TYPE_TEXT, 50, ['nullable' => false, 'default' => ''], 'Account Type'
                        )
                        ->addColumn(
                                'company_name', Table::TYPE_TEXT, 150, ['nullable' => false, 'default' => ''], 'Account company_name'
                        )
                        ->addColumn(
                                'bank_address', Table::TYPE_TEXT, 200, ['nullable' => false, 'default' => ''], 'bank_address'
                        )
                        ->addColumn(
                                'swift_code', Table::TYPE_TEXT, 50, ['nullable' => false, 'default' => ''], 'swift_code'
                        )
                        ->addColumn(
                                'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Create date'
                        )
                        ->addColumn(
                                'updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Update date'
                        )
                        ->addForeignKey(
                                $installer->getFkName('omnyfy_mcm_vendor_bank_account', 'vendor_id', 'omnyfy_vendor_vendor_entity', 'entity_id'), 'vendor_id', $installer->getTable('omnyfy_vendor_vendor_entity'), 'entity_id', Table::ACTION_CASCADE
                        )
                        ->addForeignKey(
                        $installer->getFkName('omnyfy_mcm_vendor_bank_account', 'account_type_id', 'omnyfy_mcm_vendor_bank_account_type', 'id'), 'account_type_id', $installer->getTable('omnyfy_mcm_vendor_bank_account_type'), 'id', Table::ACTION_CASCADE
                        )
                ;
                $installer->getConnection()->createTable($bank_account_Table);
            }

            if (!$setup->tableExists('omnyfy_mcm_vendor_bank_withdrawals_history')) {
                $table = $setup->getConnection()->newTable(
                                $setup->getTable('omnyfy_mcm_vendor_bank_withdrawals_history')
                        )
                        ->addColumn(
                                'id', Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'ID'
                        )
                        ->addColumn(
                                'bank_account_id', Table::TYPE_INTEGER, null, ['nullable' => false, 'default' => 0, 'unsigned' => true], 'Bank Account ID'
                        )
                        ->addColumn(
                                'vendor_id', Table::TYPE_INTEGER, null, ['nullable' => false, 'default' => 0, 'unsigned' => true], 'Vendor ID'
                        )
                        ->addColumn(
                                'withdrawals_title', Table::TYPE_TEXT, 80, ['nullable' => false, 'default' => ''], 'Bank withdrawals_title'
                        )
                        ->addColumn(
                                'received_payout_amount', Table::TYPE_TEXT, 80, ['nullable' => false, 'default' => ''], 'Account Type IDreceived_payout_amount'
                        )
                        ->addColumn(
                                'withdrawal_amount', Table::TYPE_TEXT, 80, ['nullable' => false, 'default' => ''], 'Account withdrawal_amount'
                        )
                        ->addColumn(
                                'withdrawal_date', Table::TYPE_TEXT, 80, ['nullable' => false, 'default' => ''], 'Account withdrawal_date'
                        )
                        ->addColumn(
                                'refrence_id', Table::TYPE_TEXT, 80, ['nullable' => false, 'default' => ''], 'Account refrence_id'
                        )
                        ->addColumn(
                                'available_balance', Table::TYPE_TEXT, 80, ['nullable' => false, 'default' => ''], 'Account available_balance'
                        )
                        ->addColumn(
                                'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Create date'
                        )
                        ->addColumn(
                                'updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Update date'
                        )
                        ->addForeignKey(
                                $installer->getFkName('omnyfy_mcm_vendor_bank_withdrawals_history', 'vendor_id', 'omnyfy_vendor_vendor_entity', 'entity_id'), 'vendor_id', $installer->getTable('omnyfy_vendor_vendor_entity'), 'entity_id', Table::ACTION_CASCADE
                        )
                        ->addForeignKey(
                        $installer->getFkName('omnyfy_mcm_vendor_bank_withdrawals_history', 'bank_account_id', 'omnyfy_mcm_vendor_bank_account', 'id'), 'bank_account_id', $installer->getTable('omnyfy_mcm_vendor_bank_account'), 'id', Table::ACTION_CASCADE
                        )
                ;
                $installer->getConnection()->createTable($table);
            }

            if (!$setup->tableExists('omnyfy_mcm_vendor_invoice')) {
                $table = $setup->getConnection()->newTable(
                                $setup->getTable('omnyfy_mcm_vendor_invoice')
                        )
                        ->addColumn(
                                'id', Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'ID'
                        )
                        ->addColumn(
                                'mcm_order_id', Table::TYPE_INTEGER, null, ['nullable' => false, 'default' => 0, 'unsigned' => true], 'MCM Order ID'
                        )
                        ->addColumn(
                                'invoice_id', Table::TYPE_INTEGER, null, ['nullable' => false, 'default' => 0, 'unsigned' => true], 'Invoice ID'
                        )
                        ->addColumn(
                                'vendor_id', Table::TYPE_INTEGER, null, ['nullable' => false, 'default' => 0, 'unsigned' => true], 'Vendor ID'
                        )
                        ->addColumn(
                                'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Create date'
                        )
                        ->addColumn(
                                'updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Update date'
                        )
                        ->addForeignKey(
                                $installer->getFkName('omnyfy_mcm_vendor_invoice', 'mcm_order_id', 'omnyfy_mcm_order', 'id'), 'mcm_order_id', $installer->getTable('omnyfy_mcm_order'), 'id', Table::ACTION_CASCADE
                        )
                        ->addForeignKey(
                                $installer->getFkName('omnyfy_mcm_vendor_invoice', 'invoice_id', 'sales_invoice', 'entity_id'), 'invoice_id', $installer->getTable('sales_invoice'), 'entity_id', Table::ACTION_CASCADE
                        )
                        ->addForeignKey(
                        $installer->getFkName('omnyfy_mcm_vendor_invoice', 'vendor_id', 'omnyfy_vendor_vendor_entity', 'entity_id'), 'vendor_id', $installer->getTable('omnyfy_vendor_vendor_entity'), 'entity_id', Table::ACTION_CASCADE
                );
                $installer->getConnection()->createTable($table);
            }
        }

        if (version_compare($version, '1.0.2') < 0) {
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_fees_and_charges'), 'max_seller_fee', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'comment' => 'Max seller fee',
                'after' => 'min_seller_fee'
                    ]
            );
        }

        $installer->getConnection()
                ->addColumn(
                        $installer->getTable($quoteTable), 'mcm_transaction_fee', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'default' => '0.0000',
                    'comment' => 'MCM Transaction Fee'
                        ]
        );

        $installer->getConnection()
                ->addColumn(
                        $installer->getTable($quoteAddressTable), 'mcm_transaction_fee', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'default' => '0.0000',
                    'comment' => 'MCM Transaction Fee'
                        ]
        );

        $installer->getConnection()
                ->addColumn(
                        $installer->getTable($quoteAddressTable), 'mcm_base_transaction_fee', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'default' => '0.0000',
                    'comment' => 'MCM Base Transaction Fee'
                        ]
        );

        $installer->getConnection()
                ->addColumn(
                        $installer->getTable($quoteTable), 'mcm_base_transaction_fee', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'default' => '0.0000',
                    'comment' => 'MCM Base Transaction Fee'
                        ]
        );

        $installer->getConnection()
                ->addColumn(
                        $installer->getTable($quoteTable), 'mcm_transaction_fee_tax', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'default' => '0.0000',
                    'comment' => 'MCM Transaction Fee Tax'
                        ]
        );

        $installer->getConnection()
                ->addColumn(
                        $installer->getTable($quoteTable), 'mcm_transaction_fee_incl_tax', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'default' => '0.0000',
                    'comment' => 'MCM Transaction Fee Tax'
                        ]
        );

        $installer->getConnection()
                ->addColumn(
                        $installer->getTable($quoteAddressTable), 'mcm_transaction_fee_tax', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'default' => '0.0000',
                    'comment' => 'MCM Transaction Fee Tax'
                        ]
        );

        $installer->getConnection()
                ->addColumn(
                        $installer->getTable($quoteAddressTable), 'mcm_transaction_fee_incl_tax', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'default' => '0.0000',
                    'comment' => 'MCM Transaction Fee Tax'
                        ]
        );

        $installer->getConnection()
                ->addColumn(
                        $installer->getTable($orderTable), 'mcm_transaction_fee', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'default' => '0.0000',
                    'comment' => 'MCM Transaction Fee'
                        ]
        );

        $installer->getConnection()
                ->addColumn(
                        $installer->getTable($orderTable), 'mcm_base_transaction_fee', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'default' => '0.0000',
                    'comment' => 'MCM Base Transaction Fee'
                        ]
        );

        $installer->getConnection()
                ->addColumn(
                        $installer->getTable($orderTable), 'mcm_transaction_fee_tax', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'default' => '0.0000',
                    'comment' => 'MCM Transaction Fee Tax'
                        ]
        );

        $installer->getConnection()
                ->addColumn(
                        $installer->getTable($orderTable), 'mcm_transaction_fee_incl_tax', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'default' => '0.0000',
                    'comment' => 'MCM Transaction Fee Tax'
                        ]
        );

        $installer->getConnection()
                ->addColumn(
                        $installer->getTable($invoiceTable), 'mcm_transaction_fee', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'default' => '0.0000',
                    'comment' => 'MCM Transaction Fee'
                        ]
        );

        $installer->getConnection()
                ->addColumn(
                        $installer->getTable($invoiceTable), 'mcm_base_transaction_fee', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'default' => '0.0000',
                    'comment' => 'MCM Base Transaction Fee'
                        ]
        );

        $installer->getConnection()
                ->addColumn(
                        $installer->getTable($invoiceTable), 'mcm_transaction_fee_tax', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'default' => '0.0000',
                    'comment' => 'MCM Transaction Fee Tax'
                        ]
        );

        $installer->getConnection()
                ->addColumn(
                        $installer->getTable($invoiceTable), 'mcm_transaction_fee_incl_tax', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'default' => '0.0000',
                    'comment' => 'MCM Transaction Fee Tax'
                        ]
        );

        $installer->getConnection()
                ->addColumn(
                        $installer->getTable($creditmemoTable), 'mcm_transaction_fee', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'default' => '0.0000',
                    'comment' => 'MCM Transaction Fee'
                        ]
        );

        $installer->getConnection()
                ->addColumn(
                        $installer->getTable($creditmemoTable), 'mcm_base_transaction_fee', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'default' => '0.0000',
                    'comment' => 'MCM Base Transaction Fee'
                        ]
        );

        $installer->getConnection()
                ->addColumn(
                        $installer->getTable($creditmemoTable), 'mcm_transaction_fee_tax', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'default' => '0.0000',
                    'comment' => 'MCM Transaction Fee Tax'
                        ]
        );

        $installer->getConnection()
                ->addColumn(
                        $installer->getTable($creditmemoTable), 'mcm_transaction_fee_incl_tax', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'default' => '0.0000',
                    'comment' => 'MCM Transaction Fee Tax'
                        ]
        );

        if (version_compare($version, '1.0.3') < 0) {
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_fees_and_charges'), 'status', [
                'type' => Table::TYPE_SMALLINT,
                'length' => null,
                'nullable' => false,
                'comment' => 'Fee status',
                'after' => 'disbursement_fee'
                    ]
            );
        }

        if (version_compare($version, '1.0.4') < 0) {
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_payout_history'), 'payout_amount', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => null,
                'comment' => 'Payout Amount',
                'after' => 'payout_ref'
                    ]
            );
        }
        if (version_compare($version, '1.0.5') < 0) {
            $connection->addColumn(
                    $setup->getTable($mcmVendorOrderTable), 'vendor_total', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Vendor Total exclusive tax',
                'after' => 'total_tax_onfees'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable($mcmVendorOrderTable), 'vendor_total_incl_tax', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Vendor Total inclusive tax',
                'after' => 'vendor_total'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable($mcmVendorOrderTable), 'created_at', [
                'type' => Table::TYPE_TIMESTAMP,
                'nullable' => false,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                'comment' => 'Create date',
                'after' => 'payout_action'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable($mcmVendorOrderTable), 'updated_at', [
                'type' => Table::TYPE_TIMESTAMP,
                'nullable' => false,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                'comment' => 'Update date',
                'after' => 'created_at'
                    ]
            );

            $connection->addIndex($mcmVendorOrderTable, $setup->getIdxName($mcmVendorOrderTable, ['created_at'], AdapterInterface::INDEX_TYPE_INDEX), ['created_at'], AdapterInterface::INDEX_TYPE_INDEX
            );
            $connection->addIndex($mcmVendorOrderTable, $setup->getIdxName($mcmVendorOrderTable, ['updated_at'], AdapterInterface::INDEX_TYPE_INDEX), ['updated_at'], AdapterInterface::INDEX_TYPE_INDEX
            );

            $connection->addColumn(
                    $setup->getTable($vendorPayoutHistory), 'vendor_id', [
                'type' => Table::TYPE_INTEGER,
                'nullable' => false,
                'default' => 0,
                'unsigned' => true,
                'comment' => 'Vendor ID',
                'after' => 'payout_id'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable($vendorPayoutHistory), 'vendor_order_id', [
                'type' => Table::TYPE_INTEGER,
                'nullable' => false,
                'default' => 0,
                'unsigned' => true,
                'comment' => 'Vendor Order ID',
                'after' => 'vendor_id'
                    ]
            );

            $connection->addForeignKey(
                    $installer->getFkName($vendorPayoutHistory, 'vendor_id', 'omnyfy_vendor_vendor_entity', 'entity_id'), $vendorPayoutHistory, 'vendor_id', $installer->getTable('omnyfy_vendor_vendor_entity'), 'entity_id', Table::ACTION_CASCADE
            );
            $connection->addForeignKey(
                    $installer->getFkName($vendorPayoutHistory, 'vendor_order_id', $mcmVendorOrderTable, 'id'), $vendorPayoutHistory, 'vendor_order_id', $installer->getTable($mcmVendorOrderTable), 'id', Table::ACTION_CASCADE
            );
        }

        if (version_compare($version, '1.0.7', '<')) {
            if (!$installer->tableExists('omnyfy_mcm_vendor_fee_report_admin')) {
                $vendor_fee_report_admin_Table = $installer->getConnection()->newTable(
                                $installer->getTable('omnyfy_mcm_vendor_fee_report_admin')
                        )
                        ->addColumn(
                                'id', Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'ID'
                        )->addColumn(
                                'order_id', Table::TYPE_TEXT, '200', ['nullable' => true, 'default' => null], 'Order ID'
                        )->addColumn(
                                'item_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => true, 'default' => null], 'Item ID'
                        )->addColumn(
                                'product_sku', Table::TYPE_TEXT, '100', ['nullable' => true, 'default' => null], 'Product SKU'
                        )->addColumn(
                                'product_name', Table::TYPE_TEXT, '100', ['nullable' => true, 'default' => null], 'Product Name'
                        )->addColumn(
                                'price_paid', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Price Paid'
                        )->addColumn(
                                'shipping_and_hanldling_total', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Shipping And Hanldling Total'
                        )->addColumn(
                                'discount', Table::TYPE_DECIMAL, '10,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Discount'
                        )->addColumn(
                                'order_total_value', Table::TYPE_DECIMAL, '10,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Order Total Value'
                        )->addColumn(
                                'category_commission', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Category Commission'
                        )->addColumn(
                                'seller_fee', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Seller Fee'
                        )->addColumn(
                                'disbursement_fee', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Disbursement Fee'
                        )->addColumn(
                                'total_fee', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Total Fee'
                        )->addColumn(
                                'gross_earnings', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Gross Earnings'
                        )->addColumn(
                                'tax', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Tax'
                        )->addColumn(
                                'net_earnings', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Net Earnings'
                        )
                        ->setComment(
                        'Omnyfy Marketplace Vendor Fee Report for Admin Table'
                        )
                ;
                $installer->getConnection()->createTable($vendor_fee_report_admin_Table);
            }
        }

        if (version_compare($version, '1.0.6') < 0) {
            if (!$setup->tableExists('omnyfy_mcm_sequence')) {
                $sequenceTable = $setup->getConnection()->newTable(
                                $setup->getTable('omnyfy_mcm_sequence')
                        )->addColumn(
                                'id', Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'ID'
                        )->addColumn(
                                'type', Table::TYPE_TEXT, 40, ['nullable' => true, 'default' => null], 'Type'
                        )->addColumn(
                                'prefix', Table::TYPE_TEXT, 4, ['nullable' => true, 'default' => null], 'Prefix'
                        )->addColumn(
                        'last_value', Table::TYPE_BIGINT, 12, ['unsigned' => true, 'nullable' => true, 'default' => null], 'Last Sequence Value'
                );


                $installer->getConnection()->createTable($sequenceTable);
            }
        }

        if (version_compare($version, '1.0.8', '<')) {
            if (!$installer->tableExists('omnyfy_mcm_marketplace_fee_report_admin')) {
                $marketplace_fee_report_admin_Table = $installer->getConnection()->newTable(
                                $installer->getTable('omnyfy_mcm_marketplace_fee_report_admin')
                        )
                        ->addColumn(
                                'id', Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'ID'
                        )->addColumn(
                                'order_id', Table::TYPE_TEXT, '200', ['nullable' => true, 'default' => null], 'Order ID'
                        )->addColumn(
                                'vendor_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => true, 'default' => null], 'Vendor ID'
                        )->addColumn(
                                'vendor_name', Table::TYPE_TEXT, '100', ['nullable' => true, 'default' => null], 'Vendor Name'
                        )->addColumn(
                                'item_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => true, 'default' => null], 'Item ID'
                        )->addColumn(
                                'product_sku', Table::TYPE_TEXT, '100', ['nullable' => true, 'default' => null], 'Product SKU'
                        )->addColumn(
                                'product_name', Table::TYPE_TEXT, '100', ['nullable' => true, 'default' => null], 'Product Name'
                        )->addColumn(
                                'price_paid', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Price Paid'
                        )->addColumn(
                                'shipping_and_hanldling_total', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Shipping And Hanldling Total'
                        )->addColumn(
                                'discount', Table::TYPE_DECIMAL, '10,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Discount'
                        )->addColumn(
                                'order_total_value', Table::TYPE_DECIMAL, '10,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Order Total Value'
                        )->addColumn(
                                'category_commission', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Category Commission'
                        )->addColumn(
                                'seller_fee', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Seller Fee'
                        )->addColumn(
                                'disbursement_fee', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Disbursement Fee'
                        )->addColumn(
                                'transaction_fees', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Transaction Fee'
                        )->addColumn(
                                'gross_earnings', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Gross Earnings'
                        )
                        ->setComment(
                        'Omnyfy Marketplace Fee Report Table'
                );
                $installer->getConnection()->createTable($marketplace_fee_report_admin_Table);
            }
        }
        if (version_compare($version, '1.0.9') < 0) {
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_fee_report_admin'), 'vendor_id', [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'default' => null,
                'comment' => 'Vendor ID',
                'after' => 'item_id'
                    ]
            );
        }

        if (version_compare($version, '1.0.10') < 0) {
            $connection->addColumn(
                    $setup->getTable($mcmVendorOrderTable), 'total_category_fee_tax', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Total category fee tax',
                'after' => 'total_category_fee'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable($mcmVendorOrderTable), 'total_seller_fee_tax', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Total seller fee tax',
                'after' => 'total_seller_fee'
                    ]
            );
        }

        if (version_compare($version, '1.0.11') < 0) {
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_order'), 'subtotal', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'subtotal',
                'after' => 'payout_action'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_order'), 'base_subtotal', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'base_subtotal',
                'after' => 'subtotal'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_order'), 'subtotal_incl_tax', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'subtotal_incl_tax',
                'after' => 'base_subtotal'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_order'), 'base_subtotal_incl_tax', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'base_subtotal_incl_tax',
                'after' => 'subtotal_incl_tax'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_order'), 'tax_amount', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'tax_amount',
                'after' => 'base_subtotal_incl_tax'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_order'), 'base_tax_amount', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'base_tax_amount',
                'after' => 'tax_amount'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_order'), 'discount_amount', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'discount_amount',
                'after' => 'base_tax_amount'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_order'), 'base_discount_amount', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'base_discount_amount',
                'after' => 'discount_amount'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_order'), 'shipping_amount', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'shipping_amount',
                'after' => 'base_discount_amount'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_order'), 'base_shipping_amount', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'base_shipping_amount',
                'after' => 'shipping_amount'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_order'), 'shipping_incl_tax', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'shipping_incl_tax',
                'after' => 'base_shipping_amount'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_order'), 'base_shipping_incl_tax', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'base_shipping_incl_tax',
                'after' => 'shipping_incl_tax'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_order'), 'shipping_tax', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'shipping_tax',
                'after' => 'base_shipping_incl_tax'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_order'), 'base_shipping_tax', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'base_shipping_tax',
                'after' => 'shipping_tax'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_order'), 'grand_total', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'grand_total',
                'after' => 'base_shipping_tax'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_order'), 'base_grand_total', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'base_grand_total',
                'after' => 'grand_total'
                    ]
            );
        }

        if (version_compare($version, '1.0.12') < 0) {
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_order_item'), 'tax_percentage', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Tax Percentage',
                'after' => 'tax_amount'
                    ]
            );
        }
        if (version_compare($version, '1.0.13') < 0) {
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_order_item'), 'discount_amount', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,4',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Discount',
                'after' => 'tax_amount'
                    ]
            );
        }

        if (version_compare($version, '1.0.14') < 0) {
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_order'), 'shipping_discount_amount', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,4',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Shipping Discount',
                'after' => 'base_shipping_tax'
                    ]
            );
        }
        if (version_compare($version, '1.0.15') < 0) {
            $setup->getConnection()->dropColumn($setup->getTable('omnyfy_mcm_vendor_invoice'), 'mcm_order_id');
        }

        if (version_compare($version, '1.0.16') < 0) {

            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'order_id', [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'default' => null,
                'comment' => 'order_id',
                'after' => 'vendor_id'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'payout_id', [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'default' => null,
                'comment' => 'payout_id',
                'after' => 'order_id'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'total_category_fee', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,4',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'total_category_fee',
                'after' => 'payout_id'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'total_category_fee_tax', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,4',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'total_category_fee_tax',
                'after' => 'total_category_fee'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'total_seller_fee', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,4',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'total_seller_fee',
                'after' => 'total_category_fee_tax'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'total_seller_fee_tax', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,4',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'total_seller_fee_tax',
                'after' => 'total_seller_fee'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'disbursement_fee', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,4',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'disbursement_fee',
                'after' => 'total_seller_fee_tax'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'disbursement_fee_tax', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,4',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'disbursement_fee_tax',
                'after' => 'disbursement_fee'
                    ]
            );

            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'total_tax_onfees', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,4',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'total_tax_onfees',
                'after' => 'disbursement_fee_tax'
                    ]
            );

            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'subtotal', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,4',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'subtotal',
                'after' => 'total_tax_onfees'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'base_subtotal', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,4',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'base_subtotal',
                'after' => 'subtotal'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'subtotal_incl_tax', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,4',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'subtotal_incl_tax',
                'after' => 'base_subtotal'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'base_subtotal_incl_tax', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,4',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'base_subtotal_incl_tax',
                'after' => 'subtotal_incl_tax'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'tax_amount', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,4',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'tax_amount',
                'after' => 'base_subtotal_incl_tax'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'base_tax_amount', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'base_tax_amount',
                'after' => 'tax_amount'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'discount_amount', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'discount_amount',
                'after' => 'base_tax_amount'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'base_discount_amount', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'base_discount_amount',
                'after' => 'discount_amount'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'shipping_amount', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'shipping_amount',
                'after' => 'base_discount_amount'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'base_shipping_amount', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'base_shipping_amount',
                'after' => 'shipping_amount'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'shipping_incl_tax', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'shipping_incl_tax',
                'after' => 'base_shipping_amount'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'base_shipping_incl_tax', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'base_shipping_incl_tax',
                'after' => 'shipping_incl_tax'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'shipping_tax', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'shipping_tax',
                'after' => 'base_shipping_incl_tax'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'base_shipping_tax', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'base_shipping_tax',
                'after' => 'shipping_tax'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'grand_total', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'grand_total',
                'after' => 'base_shipping_tax'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'base_grand_total', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'base_grand_total',
                'after' => 'grand_total'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_invoice'), 'shipping_discount_amount', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '10,4',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Shipping Discount',
                'after' => 'base_shipping_tax'
                    ]
            );
        }

        if (version_compare($version, '1.0.17', '<')) {
            if (!$installer->tableExists('omnyfy_mcm_vendor_fee_report_vendor')) {
                $vendor_fee_report_vendor_Table = $installer->getConnection()->newTable(
                                        $installer->getTable('omnyfy_mcm_vendor_fee_report_vendor')
                                )
                                ->addColumn(
                                        'id', Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'ID'
                                )->addColumn(
                                'order_id', Table::TYPE_TEXT, '200', ['nullable' => true, 'default' => null], 'Order ID'
                        )->addColumn(
                                'vendor_id', Table::TYPE_TEXT, '200', ['nullable' => true, 'default' => null], 'Vendor ID'
                        )->addColumn(
                                'item_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => true, 'default' => null], 'Item ID'
                        )->addColumn(
                                'product_sku', Table::TYPE_TEXT, '100', ['nullable' => true, 'default' => null], 'Product SKU'
                        )->addColumn(
                                'product_name', Table::TYPE_TEXT, '100', ['nullable' => true, 'default' => null], 'Product Name'
                        )->addColumn(
                                'price_paid', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Price Paid'
                        )->addColumn(
                                'shipping_and_hanldling_total', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Shipping And Hanldling Total'
                        )->addColumn(
                                'discount', Table::TYPE_DECIMAL, '10,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Discount'
                        )->addColumn(
                                'order_total_value', Table::TYPE_DECIMAL, '10,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Order Total Value'
                        )->addColumn(
                                'category_commission', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Category Commission'
                        )->addColumn(
                                'seller_fee', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Seller Fee'
                        )->addColumn(
                                'disbursement_fee', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Disbursement Fee'
                        )->addColumn(
                                'total_fee', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Total Fee'
                        )->addColumn(
                                'gross_earnings', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Gross Earnings'
                        )->addColumn(
                                'tax', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Tax'
                        )->addColumn(
                                'net_earnings', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Net Earnings'
                        )->setComment(
                        'Omnyfy Marketplace Vendor Fee Report for Vendor Table'
                );
                $installer->getConnection()->createTable($vendor_fee_report_vendor_Table);
            }
        }

        if (version_compare($version, '1.0.18', '<')) {
            if (!$installer->tableExists('omnyfy_mcm_vendor_shipping')) {
                $vendor_shipping_Table = $installer->getConnection()->newTable(
                                        $installer->getTable('omnyfy_mcm_vendor_shipping')
                                )
                                ->addColumn(
                                        'id', Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'ID'
                                )->addColumn(
                                'order_id', Table::TYPE_TEXT, '200', ['nullable' => true, 'default' => null], 'Order ID'
                        )->addColumn(
                                'vendor_id', Table::TYPE_TEXT, '200', ['nullable' => true, 'default' => null], 'Vendor ID'
                        )->addColumn(
                                'shipping_amount', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Shipping And Hanldling Amount'
                        )->addColumn(
                                'base_shipping_amount', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'base_shipping_amount Amount'
                        )->addColumn(
                                'shipping_incl_tax', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'shipping_incl_tax Amount'
                        )->addColumn(
                                'base_shipping_incl_tax', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'base_shipping_incl_tax Amount'
                        )->addColumn(
                                'shipping_tax', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'shipping_tax Amount'
                        )->addColumn(
                                'base_shipping_tax', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'base_shipping_tax Amount'
                        )->addColumn(
                                'shipping_discount_amount', Table::TYPE_DECIMAL, '12,2', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Shipping discount Amount'
                        )->setComment(
                        'Omnyfy Marketplace Vendor shipping Table'
                );
                $installer->getConnection()->createTable($vendor_shipping_Table);
            }
        }

        if (version_compare($version, '1.0.19') < 0) {
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_fees_and_charges'), 'tax_name', [
                'type' => Table::TYPE_TEXT,
                'length' => '255',
                'nullable' => true,
                'comment' => 'Tax Name',
                'after' => 'disbursement_fee'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_fees_and_charges'), 'tax_rate', [
                'type' => Table::TYPE_TEXT,
                'length' => '4',
                'nullable' => true,
                'default' => 0,
                'comment' => 'Tax Rate',
                'after' => 'tax_name',
                    ]
            );
        }

        if (version_compare($version, '1.0.20', '<')) {
            if (!$installer->tableExists('omnyfy_mcm_category_commission_report')) {
                $category_commission_report_Table = $installer->getConnection()->newTable($installer->getTable('omnyfy_mcm_category_commission_report'))
                        ->addColumn('id', Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'ID')
                        ->addColumn('category_id', Table::TYPE_TEXT, '200', ['nullable' => true, 'default' => null], 'Category Id')
                        ->addColumn('category_name', Table::TYPE_TEXT, '200', ['nullable' => true, 'default' => null], 'Category Name')
                        ->addColumn('category_commission_percentage', Table::TYPE_DECIMAL, '12,4', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Category Commission Percentage')
                        ->addColumn('category_commission_earned', Table::TYPE_DECIMAL, '12,4', ['unsigned' => true, 'nullable' => true, 'default' => null], 'Category Commission Earned')
                        ->setComment('Omnyfy Category Earning Report Table');
                $installer->getConnection()->createTable($category_commission_report_Table);
            }
        }

        if (version_compare($version, '1.0.21', '<')) {
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_order_item'), 'category_commission_percentage', [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'default' => null,
                'comment' => 'Category Commission Percentage',
                'after' => 'seller_fee_tax'
                    ]
            );
        }
        if (version_compare($version, '1.0.22', '<')) {
            $connection->dropTable($connection->getTableName('omnyfy_mcm_vendor_fee_report_vendor'));
        }
        if (version_compare($version, '1.0.23', '<')) {
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_marketplace_fee_report_admin'), 'created_at', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                'nullable' => false,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                'comment' => 'Create date',
                'after' => 'gross_earnings'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_fee_report_admin'), 'created_at', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                'nullable' => false,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                'comment' => 'Create date',
                'after' => 'net_earnings'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_vendor_vendor_entity'), 'created_at', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                'nullable' => false,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                'comment' => 'Create date',
                'after' => 'marketing_policy'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_category_commission_report'), 'created_at', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                'nullable' => false,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                'comment' => 'Create date',
                'after' => 'category_commission_earned'
                    ]
            );
        }

        if (version_compare($version, '1.0.24') < 0) {
            $connection->addColumn(
                    $setup->getTable($mcmVendorOrderTable), 'order_increment_id', [
                'type' => Table::TYPE_TEXT,
                'length' => '32',
                'nullable' => true,
                'comment' => 'Order Increment Id',
                'after' => 'order_id'
                    ]
            );
        }

        if (version_compare($version, '1.1.1') < 0) {
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_payout'), 'ewallet_balance', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'unsigned' => true,
                'default' => 0.00,
                'comment' => 'ewallet balance amount',
                'after' => 'ewallet_id'
                    ]
            );
        }
        if (version_compare($version, '1.1.2') < 0) {
            $connection->modifyColumn(
                    $setup->getTable('omnyfy_mcm_vendor_bank_withdrawals_history'), 'withdrawal_date', [
                'type' => Table::TYPE_TIMESTAMP,
                'nullable' => false,
                'default' => Table::TIMESTAMP_INIT,
                'comment' => 'Account withdrawal date'
                    ]
            );
        }

        if (version_compare($version, '1.1.3') < 0) {
            $connection->modifyColumn(
                    $setup->getTable($mcmVendorOrderTable), 'total_category_fee', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Total Category Fee'
                    ]
            );
            $connection->modifyColumn(
                    $setup->getTable($mcmVendorOrderTable), 'total_seller_fee', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Total Seller Fee'
                    ]
            );
            $connection->modifyColumn(
                    $setup->getTable($mcmVendorOrderTable), 'disbursement_fee', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Disbursement Fee'
                    ]
            );
            $connection->modifyColumn(
                    $setup->getTable($mcmVendorOrderTable), 'disbursement_fee_tax', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Disbursement Fee Tax'
                    ]
            );
            $connection->modifyColumn(
                    $setup->getTable($mcmVendorOrderTable), 'total_tax_onfees', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Total Tax on Fees'
                    ]
            );
            $connection->modifyColumn(
                    $setup->getTable($mcmVendorOrderTable), 'payout_amount', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Payout Amount'
                    ]
            );
            $connection->modifyColumn(
                    $setup->getTable($omnyfyMcmVendorOrderItem), 'seller_fee', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Seller Fee'
                    ]
            );
            $connection->modifyColumn(
                    $setup->getTable($omnyfyMcmVendorOrderItem), 'seller_fee_tax', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Seller Fee Tax'
                    ]
            );
            $connection->modifyColumn(
                    $setup->getTable($omnyfyMcmVendorOrderItem), 'category_commission_percentage', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Category Commission Percentage'
                    ]
            );
            $connection->modifyColumn(
                    $setup->getTable($omnyfyMcmVendorOrderItem), 'category_fee', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Category Fee'
                    ]
            );
            $connection->modifyColumn(
                    $setup->getTable($omnyfyMcmVendorOrderItem), 'category_fee_tax', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Category Fee Tax'
                    ]
            );
            $connection->modifyColumn(
                    $setup->getTable($omnyfyMcmVendorOrderItem), 'row_total', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Item row total'
                    ]
            );
            $connection->modifyColumn(
                    $setup->getTable($omnyfyMcmVendorOrderItem), 'tax_amount', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Tax Amount'
                    ]
            );
            $connection->modifyColumn(
                    $setup->getTable($omnyfyMcmVendorOrderItem), 'row_total_incl_tax', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Item Row Total Incl. Tax'
                    ]
            );
        }
        if (version_compare($version, '1.1.4') < 0) {
            $connection->modifyColumn(
                    $setup->getTable('omnyfy_mcm_vendor_payout'), 'ewallet_balance', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'ewallet balance amount',
                    ]
            );
            $connection->modifyColumn(
                    $setup->getTable('omnyfy_mcm_fees_and_charges'), 'seller_fee', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => false,
                'default' => 0.00,
                'comment' => 'Seller Fee %',
                    ]
            );
            $connection->modifyColumn(
                    $setup->getTable('omnyfy_mcm_fees_and_charges'), 'min_seller_fee', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Minimum Seller Fee',
                    ]
            );
            $connection->modifyColumn(
                    $setup->getTable('omnyfy_mcm_fees_and_charges'), 'max_seller_fee', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Max seller fee',
                    ]
            );
            $connection->modifyColumn(
                    $setup->getTable('omnyfy_mcm_fees_and_charges'), 'disbursement_fee', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => false,
                'default' => 0.00,
                'comment' => 'Disbursement Fee',
                    ]
            );
            $connection->modifyColumn(
                    $setup->getTable('omnyfy_mcm_fees_and_charges'), 'tax_rate', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '5,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Tax Rate',
                    ]
            );
            $connection->modifyColumn(
                    $setup->getTable('omnyfy_mcm_vendor_payout_history'), 'payout_amount', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Payout Amount',
                    ]
            );
            $connection->modifyColumn(
                    $setup->getTable('omnyfy_mcm_vendor_bank_withdrawals_history'), 'received_payout_amount', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Received payout amount',
                    ]
            );
            $connection->modifyColumn(
                    $setup->getTable('omnyfy_mcm_vendor_bank_withdrawals_history'), 'withdrawal_amount', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Withdrawal amount',
                    ]
            );
            $connection->modifyColumn(
                    $setup->getTable('omnyfy_mcm_vendor_bank_withdrawals_history'), 'available_balance', [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'default' => 0.00,
                'comment' => 'Available balance',
                    ]
            );
        }

        if (version_compare($version, '1.1.5') < 0) {
            $installer->getConnection()
                    ->addColumn(
                            $installer->getTable($orderTable), 'mcm_transaction_fee_surcharge', [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'nullable' => true,
                        'length' => '12,2',
                        'default' => '0.00',
                        'comment' => 'MCM Transaction Fee Surcharge',
                        'after' => 'mcm_base_transaction_fee'
                            ]
            );
        }

        if (version_compare($version, '1.1.6') < 0) {
            if (!$installer->tableExists('omnyfy_mcm_vendor_payout_invoice')) {
                $vendor_payout_invoice_table = $installer->getConnection()->newTable(
                                        $installer->getTable('omnyfy_mcm_vendor_payout_invoice')
                                )->addColumn(
                                        'id', Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'ID'
                                )->addColumn(
                                        'payout_ref', Table::TYPE_TEXT, 40, ['nullable' => false], 'Payout Reference Number'
                                )->addColumn(
                                        'increment_id', Table::TYPE_TEXT, 40, ['nullable' => true], 'Invoice Increment Id'
                                )->addColumn(
                                        'vendor_id', Table::TYPE_INTEGER, 32, ['nullable' => false, 'default' => 0, 'unsigned' => true], 'Vendor ID'
                                )->addColumn(
                                        'orders_total_incl_tax', Table::TYPE_DECIMAL, '12,2', ['nullable' => true, 'default' => 0.00, 'unsigned' => true], 'Grand totals of all orders included in this invoice'
                                )->addColumn(
                                        'orders_total_tax', Table::TYPE_DECIMAL, '12,2', ['nullable' => true, 'default' => 0.00, 'unsigned' => true], 'Tax amount included in the Order Total'
                                )->addColumn(
                                        'fees_total_incl_tax', Table::TYPE_DECIMAL, '12,2', ['nullable' => true, 'default' => 0.00, 'unsigned' => true], 'Total fees amount of all orders included in this invoice'
                                )->addColumn(
                                        'fees_total_tax', Table::TYPE_DECIMAL, '12,2', ['nullable' => true, 'default' => 0.00, 'unsigned' => true], 'Tax amount included in the fees'
                                )->addColumn(
                                        'total_earning_incl_tax', Table::TYPE_DECIMAL, '12,2', ['nullable' => true, 'default' => 0.00, 'unsigned' => true], 'Total Earnings (incl. tax)'
                                )->addColumn(
                                        'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Create date'
                                )
                                ->addColumn(
                                        'updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Update date'
                                )->addForeignKey(
                        $installer->getFkName('omnyfy_mcm_vendor_payout_invoice', 'vendor_id', 'omnyfy_vendor_vendor_entity', 'entity_id'), 'vendor_id', $installer->getTable('omnyfy_vendor_vendor_entity'), 'entity_id', Table::ACTION_CASCADE
                );
                $installer->getConnection()->createTable($vendor_payout_invoice_table);
            }

            if (!$installer->tableExists('omnyfy_mcm_vendor_payout_invoice_order')) {
                $vendor_payout_invoice_order_table = $installer->getConnection()->newTable(
                                        $installer->getTable('omnyfy_mcm_vendor_payout_invoice_order')
                                )->addColumn(
                                        'id', Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'ID'
                                )->addColumn(
                                        'invoice_id', Table::TYPE_INTEGER, 11, ['nullable' => false, 'unsigned' => true], 'Invoice Id'
                                )->addColumn(
                                        'vendor_id', Table::TYPE_INTEGER, 32, ['nullable' => false, 'default' => 0, 'unsigned' => true], 'Vendor ID'
                                )->addColumn(
                                        'order_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false], 'Order ID'
                                )->addColumn(
                                        'order_increment_id', Table::TYPE_TEXT, 32, ['nullable' => true], 'Order Increment Id'
                                )->addColumn(
                                        'order_total_incl_tax', Table::TYPE_DECIMAL, '12,2', ['nullable' => true, 'default' => 0.00, 'unsigned' => true], 'The grand total of the order'
                                )->addColumn(
                                        'order_total_tax', Table::TYPE_DECIMAL, '12,2', ['nullable' => true, 'default' => 0.00, 'unsigned' => true], 'Amount of tax included in the order'
                                )->addColumn(
                                        'fees_total_incl_tax', Table::TYPE_DECIMAL, '12,2', ['nullable' => true, 'default' => 0.00, 'unsigned' => true], 'The total amount of fees charged by the MO. Inclusive of tax'
                                )->addColumn(
                                        'fees_total_tax', Table::TYPE_DECIMAL, '12,2', ['nullable' => true, 'default' => 0.00, 'unsigned' => true], 'Tax amount included in the fees'
                                )->addColumn(
                                        'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Create date'
                                )
                                ->addColumn(
                                        'updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Update date'
                                )->addForeignKey(
                                $installer->getFkName('omnyfy_mcm_vendor_payout_invoice_order', 'invoice_id', 'omnyfy_mcm_vendor_payout_invoice', 'id'), 'invoice_id', $installer->getTable('omnyfy_mcm_vendor_payout_invoice'), 'id', Table::ACTION_CASCADE
                        )->addForeignKey(
                                $installer->getFkName('omnyfy_mcm_vendor_payout_invoice_order', 'vendor_id', 'omnyfy_vendor_vendor_entity', 'entity_id'), 'vendor_id', $installer->getTable('omnyfy_vendor_vendor_entity'), 'entity_id', Table::ACTION_CASCADE
                        )->addForeignKey(
                        $setup->getFkName('omnyfy_mcm_vendor_payout_invoice_order', 'order_id', 'sales_order', 'entity_id'), 'order_id', $setup->getTable('sales_order'), 'entity_id', Table::ACTION_CASCADE
                );
                $installer->getConnection()->createTable($vendor_payout_invoice_order_table);
            }
        }
        if (version_compare($version, '1.1.7') < 0) {
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_bank_withdrawals_history'), 'status', [
                'type' => Table::TYPE_SMALLINT,
                'length' => null,
                'nullable' => false,
                'default' => 2,
                'comment' => 'Withdrawal status. 0 = Fail, 1 = Success, 2 = In progress',
                'after' => 'available_balance'
                    ]
            );
        }
        if (version_compare($version, '1.1.8') < 0) {
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_payout'), 'third_party_account_id', [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Third party bank account id',
                'after' => 'account_ref'
                    ]
            );
        }

        if (version_compare($version, '1.1.9') < 0) {
            $connection->addColumn(
                    $setup->getTable($orderTable), 'mcm_base_transaction_fee_tax', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'nullable' => true,
                'length' => '12,2',
                'default' => '0.00',
                'comment' => 'MCM Base Transaction Fee Tax',
                'after' => 'mcm_transaction_fee_tax'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable($orderTable), 'mcm_base_transaction_fee_incl_tax', [
                'type' => Table::TYPE_DECIMAL,
                'nullable' => true,
                'length' => '12,2',
                'default' => '0.00',
                'comment' => 'MCM Base Transaction Fee Including Tax',
                'after' => 'mcm_transaction_fee_incl_tax'
                    ]
            );
        }
        if (version_compare($version, '1.1.10') < 0) {
            if ($installer->tableExists('omnyfy_mcm_vendor_payout') && $installer->tableExists('omnyfy_mcm_fees_and_charges')) {
                $connection->addForeignKey(
                        $setup->getFkName('omnyfy_mcm_vendor_payout', 'fees_charges_id', 'omnyfy_mcm_fees_and_charges', 'id'), 'omnyfy_mcm_vendor_payout', 'fees_charges_id', 'omnyfy_mcm_fees_and_charges', 'id', Table::ACTION_CASCADE
                );
            }
        }

        if (version_compare($version, '1.1.11') < 0) {
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_bank_account'), 'account_type', [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 40,
                'comment' => 'Account Type => savings or checking',
                'after' => 'account_number'
                    ]
            );
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_bank_account'), 'holder_type', [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 40,
                'comment' => 'Holder Type => personal or business',
                'after' => 'account_type'
                    ]
            );
        }
        
        if (version_compare($version, '1.1.12') < 0) {
            $connection->addColumn(
                    $setup->getTable('omnyfy_mcm_vendor_bank_account'), 'country', [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 40,
                'comment' => 'Country',
                'after' => 'account_type_id'
                    ]
            );
        }

        if (version_compare($version, '1.1.13') < 0) {
            $express = new \Zend_Db_Expr('REPLACE(`path`, "marketplacesetting", "omnyfy_mcm")');
            $connection->update(
                $setup->getTable('core_config_data'),
                ['path' => $express],
                ['`path` LIKE ?' => 'marketplacesetting%']
            );
        }

        if (version_compare($version, '1.1.14', '<')) {
            if (!$installer->tableExists('omnyfy_mcm_shipping_calculation')) {
                $vendorShippingCalculation = $installer->getConnection()->newTable(
                    $installer->getTable('omnyfy_mcm_shipping_calculation')
                )
                    ->addColumn(
                        'id', Table::TYPE_INTEGER, null, [
                            'identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true
                        ], 'ID'
                    )->addColumn(
                        'order_id', Table::TYPE_TEXT, '200', [
                            'nullable' => true, 'default' => null
                        ], 'Order ID'
                    )->addColumn(
                        'vendor_id', Table::TYPE_TEXT, '200', [
                            'nullable' => true, 'default' => null
                        ], 'Vendor ID'
                    )->addColumn(
                        'location_id', Table::TYPE_TEXT, '200', [
                            'nullable' => true, 'default' => null
                        ], 'Location ID'
                    )->addColumn(
                        'ship_by_type', Table::TYPE_TEXT, '200', [
                            'nullable' => true, 'default' => null
                        ], 'Ship By Type'
                    )->addColumn(
                        'total_charge', Table::TYPE_DECIMAL, '12,2', [
                            'unsigned' => true, 'nullable' => true, 'default' => null
                        ], 'Total Charge'
                    )->addColumn(
                        'customer_paid', Table::TYPE_DECIMAL, '12,2', [
                            'unsigned' => true, 'nullable' => true, 'default' => null
                        ], 'Customer Paid'
                    )->setComment(
                        'Omnyfy Marketplace Shipping Calculation'
                    );

                $installer->getConnection()->createTable($vendorShippingCalculation);
            }
        }

        if (version_compare($version, '1.1.15') < 0) {
            $connection->addColumn(
                $setup->getTable('omnyfy_mcm_vendor_payout_invoice'),
                'shipping_total_for_order',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'comment' => 'Shipping Total For Order'
                ]
            );

            $connection->addColumn(
                $setup->getTable('omnyfy_mcm_vendor_payout_invoice_order'),
                'shipping_total_for_order',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'comment' => 'Shipping Total For Order'
                ]
            );
        }

        if (version_compare($version, '1.1.16') < 0) {
            $connection->addColumn(
                $setup->getTable('omnyfy_mcm_shipping_calculation'), 'type', [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => 200,
                    'comment' => 'Type of record'
                ]
            );
        }

        if (version_compare($version, '1.1.17') < 0) {
            $connection->addColumn(
                $setup->getTable('omnyfy_mcm_vendor_order'), 'payout_calculated', [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'comment' => 'Type of record',
                    'default' => 0
                ]
            );
        }

        if (version_compare($version, '1.1.18') < 0) {
            $connection->addColumn(
                $setup->getTable('omnyfy_mcm_vendor_order'), 'payout_shipping', [
                    'scale'     => 2,
                    'precision' => 12,
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'comment' => 'Payout Shipping',
                    'default' => 0
                ]
            );
        }

        if (version_compare($version, '1.1.19') < 0) {
            $setup->getConnection()->changeColumn(
                $setup->getTable('omnyfy_mcm_vendor_payout_invoice'),
                'shipping_total_for_order',
                'shipping_total_for_order',
                [
                    'scale'     => 2,
                    'precision' => 12,
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'comment' => 'Shipping Total For Order'
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('omnyfy_mcm_vendor_payout_invoice_order'),
                'shipping_total_for_order',
                'shipping_total_for_order',
                [
                    'scale'     => 2,
                    'precision' => 12,
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'comment' => 'Shipping Total For Order'
                ]
            );

        }



        $installer->endSetup();
    }

}
