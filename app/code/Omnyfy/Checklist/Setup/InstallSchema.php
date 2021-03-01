<?php


namespace Omnyfy\Checklist\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $table_omnyfy_checklist_checklist = $setup->getConnection()->newTable($setup->getTable('omnyfy_checklist_checklist'));

        
        $table_omnyfy_checklist_checklist->addColumn(
            'checklist_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        
        $table_omnyfy_checklist_checklist->addColumn(
            'checklist_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => False],
            'checklist_title'
        );
        

        
        $table_omnyfy_checklist_checklist->addColumn(
            'checklist_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'checklist_description'
        );
        

        
        $table_omnyfy_checklist_checklist->addColumn(
            'checklist_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'checklist_status'
        );
        

        $table_omnyfy_checklist_checklistitems = $setup->getConnection()->newTable($setup->getTable('omnyfy_checklist_checklistitems'));

        
        $table_omnyfy_checklist_checklistitems->addColumn(
            'checklistitems_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );

        
        $table_omnyfy_checklist_checklistitems->addColumn(
            'checklist_item_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'checklist_item_title'
        );
        

        
        $table_omnyfy_checklist_checklistitems->addColumn(
            'checklist_item_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'checklist_item_description'
        );

        $table_omnyfy_checklist_checklistitems->addColumn(
            'checklist_item_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Sort Order'
        );

        $table_omnyfy_checklist_checklistitems->addColumn(
            'checklist_item_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'checklist_item_status'
        );
        

        $table_omnyfy_checklist_checklistitemoptions = $setup->getConnection()->newTable($setup->getTable('omnyfy_checklist_checklistitemoptions'));

        
        $table_omnyfy_checklist_checklistitemoptions->addColumn(
            'checklistitemoptions_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        

        
        $table_omnyfy_checklist_checklistitemoptions->addColumn(
            'option_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'option_id'
        );
        

        
        $table_omnyfy_checklist_checklistitemoptions->addColumn(
            'item_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'item_id'
        );
        

        
        $table_omnyfy_checklist_checklistitemoptions->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'name'
        );
        

        
        $table_omnyfy_checklist_checklistitemoptions->addColumn(
            'cms_article_link',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'CMS Article ID'
        );
        

        $table_omnyfy_checklist_checklistitemuploads = $setup->getConnection()->newTable($setup->getTable('omnyfy_checklist_checklistitemuploads'));

        
        $table_omnyfy_checklist_checklistitemuploads->addColumn(
            'checklistitemuploads_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );

        $table_omnyfy_checklist_checklistitemuploads->addColumn(
            'item_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Check list item ID'
        );


        $table_omnyfy_checklist_checklistitemuploads->addColumn(
            'upload_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Name of the button'
        );


        $table_omnyfy_checklist_checklistitemuseroptions = $setup->getConnection()->newTable($setup->getTable('omnyfy_checklist_checklistitemuseroptions'));

        
        $table_omnyfy_checklist_checklistitemuseroptions->addColumn(
            'checklistitemuseroptions_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        

        
        $table_omnyfy_checklist_checklistitemuseroptions->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'user_id'
        );
        

        
        $table_omnyfy_checklist_checklistitemuseroptions->addColumn(
            'item_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'item_id'
        );
        

        
        $table_omnyfy_checklist_checklistitemuseroptions->addColumn(
            'option_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'option_id'
        );
        

        $table_omnyfy_checklist_checklistitemuseruploads = $setup->getConnection()->newTable($setup->getTable('omnyfy_checklist_checklistitemuseruploads'));

        
        $table_omnyfy_checklist_checklistitemuseruploads->addColumn(
            'checklistitemuseruploads_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        

        
        $table_omnyfy_checklist_checklistitemuseruploads->addColumn(
            'upload_is',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'upload_is'
        );
        

        
        $table_omnyfy_checklist_checklistitemuseruploads->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'user_id'
        );
        

        
        $table_omnyfy_checklist_checklistitemuseruploads->addColumn(
            'item_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'item_id'
        );
        

        
        $table_omnyfy_checklist_checklistitemuseruploads->addColumn(
            'upload_link',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'upload_link'
        );
        

        $setup->getConnection()->createTable($table_omnyfy_checklist_checklistitemuseruploads);

        $setup->getConnection()->createTable($table_omnyfy_checklist_checklistitemuseroptions);

        $setup->getConnection()->createTable($table_omnyfy_checklist_checklistitemuploads);

        $setup->getConnection()->createTable($table_omnyfy_checklist_checklistitemoptions);

        $setup->getConnection()->createTable($table_omnyfy_checklist_checklistitems);

        $setup->getConnection()->createTable($table_omnyfy_checklist_checklist);

        $setup->endSetup();
    }
}
