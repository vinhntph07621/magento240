<?php
namespace Omnyfy\Stripe\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements  UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $mcmVendorOrderTable = 'omnyfy_mcm_vendor_order';
        $mcmVendorWithdrawHistoryTable = 'omnyfy_mcm_vendor_bank_withdrawals_history';

        $version = $context->getVersion();
        $connection = $setup->getConnection();

        if (version_compare($version, '1.0.1') < 0) {
            $connection->addColumn(
                $setup->getTable($mcmVendorOrderTable), 'stripe_transfer_id', [
                    'type' => Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => true,
                    'comment' => 'Stripe Transfer Id',
                ]
            );
        }

        if (version_compare($version, '1.0.2') < 0) {
            $connection->addColumn(
                $setup->getTable($mcmVendorWithdrawHistoryTable), 'stripe_payout_id', [
                    'type' => Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => true,
                    'comment' => 'Stripe Payout Id',
                ]
            );
            $connection->addColumn(
                $setup->getTable($mcmVendorWithdrawHistoryTable), 'payout_ext_info', [
                    'type' => Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => true,
                    'comment' => 'Payout Extra Info',
                ]
            );
        }

        if (version_compare($version, '1.0.3') < 0) {
            $connection->addColumn(
                $setup->getTable($mcmVendorOrderTable), 'stripe_transfer_id', [
                    'type' => Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => true,
                    'comment' => 'Stripe Transfer Id',
                ]
            );
            $connection->dropColumn($setup->getTable($mcmVendorOrderTable), 'stripe_transfer_id');
            $connection->dropColumn($setup->getTable($mcmVendorWithdrawHistoryTable), 'stripe_payout_id');
            $connection->dropColumn($setup->getTable($mcmVendorWithdrawHistoryTable), 'payout_ext_info');

            /**
             * Create table 'omnyfy_vendor_gallery_album_location'
             */
            $table = $installer->getConnection()
                ->newTable($installer->getTable('omnyfy_stripe_withdrawals_webhooks_data'))
                ->addColumn(
                    'id', Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Data ID'
                )->addColumn(
                    'stripe_payout_id', Table::TYPE_TEXT, 255, ['nullable' => false], 'Stripe Payout ID'
                )->addColumn(
                    'payout_ext_info', Table::TYPE_TEXT, 255, ['nullable' => false], 'Payout Extra Info'
                )->setComment(
                    'Omnyfy Marketplace Vendor Withdraw stripe webhooks'
                );
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}
