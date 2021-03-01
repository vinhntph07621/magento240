<?php
namespace Omnyfy\Rma\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     *
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $rmaTable = $setup->getConnection()->getTableName('mst_rma_item');
        if ($setup->tableExists($rmaTable) && !$setup->getConnection()->tableColumnExists($rmaTable, 'vendor_id')) {
            $setup->getConnection()->addColumn(
                $rmaTable,
                'vendor_id',
                [
                    'type'      => Table::TYPE_INTEGER,
                    'nullable'  => false,
                    'comment'   => 'Vendor Id',
                    'unsigned'  => true,
                    'after'     => 'order_item_id'
                ]
            )
            ;
        }

        $returnAddressTable = $setup->getConnection()->getTableName('mst_rma_return_address');
        if ($setup->tableExists($returnAddressTable) && !$setup->getConnection()->tableColumnExists($returnAddressTable, 'vendor_id')) {
            $setup->getConnection()->addColumn(
                $returnAddressTable,
                'vendor_id',
                [
                    'type'      => Table::TYPE_INTEGER,
                    'nullable'  => false,
                    'comment'   => 'Vendor Id',
                    'unsigned'  => true
                ]
            )
            ;
        }
    }
}
