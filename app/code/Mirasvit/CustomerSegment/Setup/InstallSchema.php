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

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mirasvit\CustomerSegment\Api\Data\Segment\CustomerInterface;
use Mirasvit\CustomerSegment\Api\Data\Segment\HistoryInterface;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $tables = [];

        $installer->startSetup();

        $tables[] = $this->createSegmentTable($installer);
        $tables[] = $this->createSegmentCustomerTable($installer);
        $tables[] = $this->createSegmentHistoryTable($installer);

        foreach ($tables as $table) {
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }

    /**
     * Create 'mst_customersegment_segment' table
     *
     * @param SchemaSetupInterface $installer
     *
     * @return \Magento\Framework\DB\Ddl\Table
     * @throws \Zend_Db_Exception
     */
    protected function createSegmentTable(SchemaSetupInterface $installer)
    {
        return $installer->getConnection()->newTable(
            $installer->getTable('mst_customersegment_segment')
        )->addColumn(
            SegmentInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Segment ID'
        )->addColumn(
            SegmentInterface::TITLE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Title'
        )->addColumn(
            SegmentInterface::DESCRIPTION,
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            'Description'
        )->addColumn(
            SegmentInterface::TYPE,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Segment Customer Type'
        )->addColumn(
            SegmentInterface::WEBSITE_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Website ID'
        )->addColumn(
            SegmentInterface::CONDITIONS_SERIALIZED,
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            'Serialized Conditions'
        )->addColumn(
            SegmentInterface::PRIORITY,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'unsigned' => true],
            'Priority'
        )->addColumn(
            SegmentInterface::IS_MANUAL,
            Table::TYPE_BOOLEAN,
            null,
            ['nullable' => true, 'default' => false],
            'Is Manually Managed'
        )->addColumn(
            SegmentInterface::TO_GROUP_ID,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'To Group ID'
        )->addColumn(
            SegmentInterface::STATUS,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Status'
        )->addColumn(
            SegmentInterface::CREATED_AT,
            Table::TYPE_DATETIME,
            null,
            ['nullable' => false],
            'Created At'
        )->addColumn(
            SegmentInterface::UPDATED_AT,
            Table::TYPE_DATETIME,
            null,
            ['nullable' => false],
            'Updated At'
        );
    }

    /**
     * Create 'mst_customersegment_segment_customer' table
     *
     * @param SchemaSetupInterface $installer
     *
     * @return \Magento\Framework\DB\Ddl\Table
     * @throws \Zend_Db_Exception
     */
    protected function createSegmentCustomerTable(SchemaSetupInterface $installer)
    {
        return $installer->getConnection()->newTable(
            $installer->getTable('mst_customersegment_segment_customer')
        )->addColumn(
            CustomerInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Segment Customer ID'
        )->addColumn(
            CustomerInterface::SEGMENT_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Segment ID'
        )->addColumn(
            CustomerInterface::CUSTOMER_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Customer ID'
        )->addColumn(
            CustomerInterface::EMAIL,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Customer email'
        )->addColumn(
            CustomerInterface::CREATED_AT,
            Table::TYPE_DATETIME,
            null,
            ['nullable' => false],
            'Created At'
        )->addIndex(
            $installer->getIdxName(
                'mst_customersegment_segment_customer',
                ['segment_id', 'email']
            ),
            ['segment_id', 'email'],
            ['type' => 'unique']
        )->addForeignKey(
            $installer->getFkName('mst_customersegment_segment_customer', 'segment_id', 'mst_customersegment_segment', 'segment_id'),
            'segment_id',
            $installer->getTable('mst_customersegment_segment'),
            'segment_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('mst_customersegment_segment_customer', 'customer_id', 'customer_entity', 'entity_id'),
            'customer_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
        );
    }

    /**
     * Create 'mst_customersegment_segment_history' table
     *
     * @param SchemaSetupInterface $installer
     *
     * @return \Magento\Framework\DB\Ddl\Table
     * @throws \Zend_Db_Exception
     */
    protected function createSegmentHistoryTable(SchemaSetupInterface $installer)
    {
        return $installer->getConnection()->newTable(
            $installer->getTable('mst_customersegment_segment_history')
        )->addColumn(
            HistoryInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
            'History ID'
        )->addColumn(
            HistoryInterface::SEGMENT_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Segment ID'
        )->addColumn(
            HistoryInterface::ACTION,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Action'
        )->addColumn(
            HistoryInterface::AFFECTED_ROWS,
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => false],
            'Number of Impacted Rows'
        )->addColumn(
            HistoryInterface::TYPE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'History Action Type'
        )->addColumn(
            HistoryInterface::MESSAGE,
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => true],
            'Message'
        )->addColumn(
            HistoryInterface::CREATED_AT,
            Table::TYPE_DATETIME,
            null,
            ['nullable' => false],
            'Created At'
        );
    }
}
