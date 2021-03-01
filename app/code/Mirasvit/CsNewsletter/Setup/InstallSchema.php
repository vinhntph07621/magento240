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



namespace Mirasvit\CsNewsletter\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mirasvit\CsNewsletter\Api\Repository\SegmentNewsletterRepositoryInterface;
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

        $tables[] = $this->createSegmentNewsletterTable($installer);

        foreach ($tables as $table) {
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }

    /**
     * Create 'mst_customersegment_newsletter' table.
     *
     * Holds association between customer segment and newsletter queue entities.
     *
     * @param SchemaSetupInterface $installer
     *
     * @return \Magento\Framework\DB\Ddl\Table
     * @throws \Zend_Db_Exception
     */
    protected function createSegmentNewsletterTable(SchemaSetupInterface $installer)
    {
        return $installer->getConnection()->newTable(
            $installer->getTable(SegmentNewsletterRepositoryInterface::TABLE_NAME)
        )->addColumn(
            SegmentNewsletterRepositoryInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Customer Segment Newsletter Queue'
        )->addColumn(
            SegmentNewsletterRepositoryInterface::SEGMENT_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Segment ID'
        )->addColumn(
            SegmentNewsletterRepositoryInterface::QUEUE_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Newsletter Queue ID'
        )->addForeignKey(
            $installer->getFkName('mst_customersegment_newsletter', 'segment_id', 'mst_customersegment_segment', 'segment_id'),
            SegmentNewsletterRepositoryInterface::SEGMENT_ID,
            $installer->getTable('mst_customersegment_segment'),
            SegmentInterface::ID,
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('mst_customersegment_newsletter', 'queue_id', 'newsletter_queue', 'queue_id'),
            SegmentNewsletterRepositoryInterface::QUEUE_ID,
            $installer->getTable('newsletter_queue'),
            'queue_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
    }
}
