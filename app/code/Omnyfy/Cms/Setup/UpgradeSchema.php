<?php

namespace Omnyfy\Cms\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Cms update
 */
class UpgradeSchema implements UpgradeSchemaInterface {

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $setup->startSetup();

        $version = $context->getVersion();
        $connection = $setup->getConnection();

        if (version_compare($version, '2.0.1') < 0) {

            foreach (['omnyfy_cms_article_relatedarticle', 'omnyfy_cms_article_relatedproduct'] as $tableName) {
                // Get module table
                $tableName = $setup->getTable($tableName);

                // Check if the table already exists
                if ($connection->isTableExists($tableName) == true) {

                    $columns = [
                        'position' => [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                            'nullable' => false,
                            'comment' => 'Position',
                        ],
                    ];

                    foreach ($columns as $name => $definition) {
                        $connection->addColumn($tableName, $name, $definition);
                    }
                }
            }

            $connection->addColumn(
                    $setup->getTable('omnyfy_cms_article'), 'featured_img', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Thumbnail Image',
                    ]
            );
        }

        if (version_compare($version, '2.2.0') < 0) {
            /* Add author field to articles tabel */
            $connection->addColumn(
                    $setup->getTable('omnyfy_cms_article'), 'author_id', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Author ID',
                    ]
            );

            $connection->addIndex(
                    $setup->getTable('omnyfy_cms_article'), $setup->getIdxName($setup->getTable('omnyfy_cms_article'), ['author_id']), ['author_id']
            );
        }


        if (version_compare($version, '2.2.5') < 0) {
            /* Add layout field to articles and category tabels */
            foreach (['omnyfy_cms_article', 'omnyfy_cms_category'] as $table) {
                $table = $setup->getTable($table);
                $connection->addColumn(
                        $setup->getTable($table), 'page_layout', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Article Layout',
                        ]
                );

                $connection->addColumn(
                        $setup->getTable($table), 'layout_update_xml', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '64k',
                    'nullable' => true,
                    'comment' => 'Article Layout Update Content',
                        ]
                );

                $connection->addColumn(
                        $setup->getTable($table), 'custom_theme', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 100,
                    'nullable' => true,
                    'comment' => 'Article Custom Theme',
                        ]
                );

                $connection->addColumn(
                        $setup->getTable($table), 'custom_layout', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Article Custom Template',
                        ]
                );

                $connection->addColumn(
                        $setup->getTable($table), 'custom_layout_update_xml', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '64k',
                    'nullable' => true,
                    'comment' => 'Article Custom Layout Update Content',
                        ]
                );

                $connection->addColumn(
                        $setup->getTable($table), 'custom_theme_from', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                    'nullable' => true,
                    'comment' => 'Article Custom Theme Active From Date',
                        ]
                );

                $connection->addColumn(
                        $setup->getTable($table), 'custom_theme_to', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                    'nullable' => true,
                    'comment' => 'Article Custom Theme Active To Date',
                        ]
                );
            }
        }

        if (version_compare($version, '2.3.0') < 0) {
            /* Add meta title field to articles tabel */
            $connection->addColumn(
                    $setup->getTable('omnyfy_cms_article'), 'meta_title', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Article Meta Title',
                'after' => 'title'
                    ]
            );

            /* Add og tags fields to article tabel */
            foreach (['type', 'img', 'description', 'title'] as $type) {
                $connection->addColumn(
                        $setup->getTable('omnyfy_cms_article'), 'og_' . $type, [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Article OG ' . ucfirst($type),
                    'after' => 'identifier'
                        ]
                );
            }

            /* Add meta title field to category tabel */
            $connection->addColumn(
                    $setup->getTable('omnyfy_cms_category'), 'meta_title', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Category Meta Title',
                'after' => 'title'
                    ]
            );

            /**
             * Create table 'omnyfy_cms_tag'
             */
            $table = $setup->getConnection()->newTable(
                            $setup->getTable('omnyfy_cms_tag')
                    )->addColumn(
                            'tag_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true], 'Tag ID'
                    )->addColumn(
                            'title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true], 'Tag Title'
                    )->addColumn(
                            'identifier', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 100, ['nullable' => true, 'default' => null], 'Tag String Identifier'
                    )->addIndex(
                            $setup->getIdxName('omnyfy_cms_tag', ['identifier']), ['identifier']
                    )->setComment(
                    'Omnyfy Cms Tag Table'
            );
            $setup->getConnection()->createTable($table);

            /**
             * Create table 'omnyfy_cms_article_tag'
             */
            $table = $setup->getConnection()->newTable(
                            $setup->getTable('omnyfy_cms_article_tag')
                    )->addColumn(
                            'article_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false, 'primary' => true], 'Article ID'
                    )->addColumn(
                            'tag_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false, 'primary' => true], 'Tag ID'
                    )->addIndex(
                            $setup->getIdxName('omnyfy_cms_article_tag', ['tag_id']), ['tag_id']
                    )->addForeignKey(
                            $setup->getFkName('omnyfy_cms_article_tag', 'article_id', 'omnyfy_cms_article', 'article_id'), 'article_id', $setup->getTable('omnyfy_cms_article'), 'article_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )->addForeignKey(
                            $setup->getFkName('omnyfy_cms_article_tag', 'tag_id', 'omnyfy_cms_tag', 'tag_id'), 'tag_id', $setup->getTable('omnyfy_cms_tag'), 'tag_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )->setComment(
                    'Omnyfy Cms Article To Category Linkage Table'
            );
            $setup->getConnection()->createTable($table);
        }

        if (version_compare($version, '3.0.2') < 0) {
            /* Add is_learn, category_icon, category_snippet fields to category tabel */
            $connection->addColumn(
                    $setup->getTable('omnyfy_cms_category'), 'is_learn', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length' => null,
                'nullable' => true,
                'comment' => 'Is Learn Page Category'
                    ]
            );

            $connection->addColumn(
                    $setup->getTable('omnyfy_cms_category'), 'category_icon', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Category Icon',
                    ]
            );

            $connection->addColumn(
                    $setup->getTable('omnyfy_cms_category'), 'category_snippet', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => '2M',
                'nullable' => true,
                'comment' => 'Category Snippet'
                    ]
            );

            /**
             * Create table 'omnyfy_cms_user_type'
             */
            $table = $setup->getConnection()->newTable(
                            $setup->getTable('omnyfy_cms_user_type')
                    )->addColumn(
                            'id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true], 'User Type ID'
                    )->addColumn(
                            'user_type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true], 'User Type'
                    )->addColumn(
                            'status', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, ['nullable' => false], 'User Type Status'
                    )->addColumn(
                            'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'User Type Creation time'
                    )->addColumn(
                            'modified_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'User Type Last Modification Time'
                    )->setComment(
                    'Omnyfy Cms User Type Table'
            );
            $setup->getConnection()->createTable($table);

            /**
             * Create table 'omnyfy_cms_article_user_type'
             */
            $table = $setup->getConnection()->newTable(
                            $setup->getTable('omnyfy_cms_article_user_type')
                    )->addColumn(
                            'article_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false, 'primary' => true], 'Article ID'
                    )->addColumn(
                            'user_type_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false, 'primary' => true], 'User Type ID'
                    )->addIndex(
                            $setup->getIdxName('omnyfy_cms_article_user_type', ['user_type_id']), ['user_type_id']
                    )->addForeignKey(
                            $setup->getFkName('omnyfy_cms_article_user_type', 'article_id', 'omnyfy_cms_article', 'article_id'), 'article_id', $setup->getTable('omnyfy_cms_article'), 'article_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )->addForeignKey(
                            $setup->getFkName('omnyfy_cms_article_user_type', 'user_type_id', 'omnyfy_cms_user_type', 'id'), 'user_type_id', $setup->getTable('omnyfy_cms_user_type'), 'id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )->setComment(
                    'Omnyfy Cms Article To User Type Linkage Table'
            );
            $setup->getConnection()->createTable($table);

            /**
             * Create table 'omnyfy_cms_tool_template'
             */
            $table = $setup->getConnection()->newTable(
                            $setup->getTable('omnyfy_cms_tool_template')
                    )->addColumn(
                            'id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true], 'ID'
                    )->addColumn(
                            'title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Title'
                    )->addColumn(
                            'short_description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', ['nullable' => false], 'Short description'
                    )->addColumn(
                            'link', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Link'
                    )->addColumn(
                            'status', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, ['nullable' => false], 'Status'
                    )->addColumn(
                            'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'Creation time'
                    )->addColumn(
                            'modified_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'Last Modification Time'
                    )->setComment(
                    'Omnyfy Cms Tool Template Table'
            );
            $setup->getConnection()->createTable($table);

            /**
             * Create table 'omnyfy_cms_article_tool_template'
             */
            $table = $setup->getConnection()->newTable(
                            $setup->getTable('omnyfy_cms_article_tool_template')
                    )->addColumn(
                            'article_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false, 'primary' => true], 'Article ID'
                    )->addColumn(
                            'tool_template_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false, 'primary' => true], 'Tool and Template ID'
                    )->addIndex(
                            $setup->getIdxName('omnyfy_cms_article_tool_template', ['tool_template_id']), ['tool_template_id']
                    )->addForeignKey(
                            $setup->getFkName('omnyfy_cms_article_tool_template', 'article_id', 'omnyfy_cms_article', 'article_id'), 'article_id', $setup->getTable('omnyfy_cms_article'), 'article_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )->addForeignKey(
                            $setup->getFkName('omnyfy_cms_article_tool_template', 'tool_template_id', 'omnyfy_cms_tool_template', 'id'), 'tool_template_id', $setup->getTable('omnyfy_cms_tool_template'), 'id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )->setComment(
                    'Omnyfy Cms Article To Tool and Template Linkage Table'
            );
            $setup->getConnection()->createTable($table);

            /**
             * Create table 'omnyfy_cms_article_service_category'
             */
            $table = $setup->getConnection()->newTable(
                            $setup->getTable('omnyfy_cms_article_service_category')
                    )->addColumn(
                            'article_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false, 'primary' => true], 'Article ID'
                    )->addColumn(
                            'catelog_category_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false, 'primary' => true], 'Catelog Category ID'
                    )->addIndex(
                            $setup->getIdxName('omnyfy_cms_article_service_category', ['catelog_category_id']), ['catelog_category_id']
                    )->addForeignKey(
                            $setup->getFkName('omnyfy_cms_article_service_category', 'article_id', 'omnyfy_cms_article', 'article_id'), 'article_id', $setup->getTable('omnyfy_cms_article'), 'article_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )->setComment(
                    'Omnyfy Cms Article To Catelog service category Linkage Table'
            );
            $setup->getConnection()->createTable($table);

            /**
             * Create table 'omnyfy_cms_article_vendor'
             */
            $table = $setup->getConnection()->newTable(
                            $setup->getTable('omnyfy_cms_article_vendor')
                    )->addColumn(
                            'article_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false, 'primary' => true], 'Article ID'
                    )->addColumn(
                            'vendor_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false, 'primary' => true], 'Vendor ID'
                    )->addIndex(
                            $setup->getIdxName('omnyfy_cms_article_vendor', ['vendor_id']), ['vendor_id']
                    )->addForeignKey(
                            $setup->getFkName('omnyfy_cms_article_vendor', 'article_id', 'omnyfy_cms_article', 'article_id'), 'article_id', $setup->getTable('omnyfy_cms_article'), 'article_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )->addForeignKey(
                            $setup->getFkName('omnyfy_cms_article_vendor', 'vendor_id', 'omnyfy_vendor_location_entity', 'entity_id'), 'vendor_id', $setup->getTable('omnyfy_vendor_location_entity'), 'entity_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )->setComment(
                    'Omnyfy Cms Article To Vendor Linkage Table'
            );
            $setup->getConnection()->createTable($table);
        }

        if (version_compare($version, '3.0.4') < 0) {

            foreach (['omnyfy_cms_article_service_category', 'omnyfy_cms_article_tool_template', 'omnyfy_cms_article_vendor', 'omnyfy_cms_article_category', 'omnyfy_cms_article'] as $tableName) {
                // Get module table
                $tableName = $setup->getTable($tableName);

                // Check if the table already exists
                if ($connection->isTableExists($tableName) == true) {

                    $columns = [
                        'position' => [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                            'nullable' => false,
                            'comment' => 'Position',
                        ],
                    ];

                    foreach ($columns as $name => $definition) {
                        $connection->addColumn($tableName, $name, $definition);
                    }
                }
            }

            $connection->addColumn(
                    $setup->getTable('omnyfy_cms_article'), 'article_counter', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length' => null,
                'nullable' => true,
                'comment' => 'Artilce View Counter'
                    ]
            );

            $connection->addColumn(
                    $setup->getTable('omnyfy_cms_category'), 'category_banner', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Category Banner',
                    ]
            );
        }

        if (version_compare($version, '3.0.5') < 0) {
            /**
             * Create table 'omnyfy_cms_country'
             */
            $table = $setup->getConnection()->newTable(
                            $setup->getTable('omnyfy_cms_country')
                    )->addColumn(
                            'id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true], 'ID'
                    )->addColumn(
                            'country_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 2, ['nullable' => false], 'Country Id in ISO-2'
                    )->addColumn(
                            'country_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Country Name'
                    )->addColumn(
                            'introduction', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 1024, ['nullable' => false], 'Country Introduction'
                    )->addColumn(
                            'population', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Country Population'
                    )->addColumn(
                            'income_level', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, ['nullable' => false], 'Income Level, 1 = Low-income, 2= Lower middle-income, 3= Upper middle-income, 4 = High-income'
                    )->addColumn(
                            'gni_per_capita', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'GNI Per Capita'
                    )->addColumn(
                            'gdp', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 45, ['nullable' => false], 'GDP'
                    )->addColumn(
                            'gdp_growth', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 45, ['nullable' => false], 'GSP Growth'
                    )->addColumn(
                            'gdp_forecast', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 45, ['nullable' => false], 'GDP Forecast'
                    )->addColumn(
                            'total_exports', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 45, ['nullable' => false], 'Total Exports'
                    )->addColumn(
                            'currency_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 3, ['nullable' => false], 'Currency ID'
                    )->addColumn(
                            'general_info_category', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, NULL, ['nullable' => false], 'Country General Information Category'
                    )->addColumn(
                            'industry_info_category', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, NULL, ['nullable' => false], 'Country Industry Information Category'
                    )->addColumn(
                            'flag_image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], "Image of the country flag on the 'find markets by country' page"
                    )->addColumn(
                            'banner_image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], "Banner image of the country on the 'find markets by country' page"
                    )->addColumn(
                            'background_image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], "Hero image on the country profile page"
                    )->addColumn(
                            'featured_callout', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, ['nullable' => true], '1=enable, 0 = disable, Enabling this will add a featured callout panel on the country profile page between the recommended items and industry information panels.'
                    )->addColumn(
                            'callout_image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true], "An image for the featured callout panel."
                    )->addColumn(
                            'callout_content', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 1024, ['nullable' => true], 'Content on the featured callout panel next to the featured image.'
                    )->addColumn(
                            'status', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 2, ['nullable' => false], '1=enable, 0 = disable'
                    )->addColumn(
                            'identifier', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true], "String Identifier"
                    )->addColumn(
                            'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'Created at timestamp'
                    )->addColumn(
                            'modified_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'Modified at timestamp'
                    )->addForeignKey(
                            $setup->getFkName('omnyfy_cms_country_directory_country_fk', 'country_id', 'directory_country', 'country_id'), 'country_id', $setup->getTable('directory_country'), 'country_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )->addForeignKey(
                            $setup->getFkName('omnyfy_cms_country_general_info_category_cms_category_fk', 'general_info_category', 'omnyfy_cms_category', 'category_id'), 'general_info_category', $setup->getTable('omnyfy_cms_category'), 'category_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )->addForeignKey(
                            $setup->getFkName('omnyfy_cms_country_industry_info_category_cms_category_fk', 'industry_info_category', 'omnyfy_cms_category', 'category_id'), 'industry_info_category', $setup->getTable('omnyfy_cms_category'), 'category_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )->setComment(
                    'Omnyfy Cms Country Table'
            );
            $setup->getConnection()->createTable($table);
        }

        if (version_compare($version, '3.0.6') < 0) {
            /**
             * Update table 'omnyfy_cms_category'
             */
            // Get module table
            $tableName = $setup->getTable('omnyfy_cms_category');

            // Check if the table already exists
            if ($connection->isTableExists($tableName) == true) {

                $connection->addColumn(
                        $setup->getTable('omnyfy_cms_category'), 'is_specific_country', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'For Specific Country',
                        ]
                );

                $connection->addColumn(
                        $setup->getTable('omnyfy_cms_category'), 'country_id', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Country ID',
                        ]
                );
                $connection->addForeignKey(
                        $installer->getFkName('omnyfy_cms_category', 'country_id', 'omnyfy_cms_country', 'id'), $setup->getTable('omnyfy_cms_category'), 'country_id', $installer->getTable('omnyfy_cms_country'), 'id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            }
        }

        if (version_compare($version, '3.0.7') < 0) {
            /**
             * Update table 'omnyfy_cms_category'
             */
            // Get module table
            $tableName = $setup->getTable('omnyfy_cms_country');

            // Check if the table already exists
            if ($connection->isTableExists($tableName) == true) {

                $connection->addColumn(
                        $setup->getTable('omnyfy_cms_country'), 'position_left', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Map Pin Position From Left',
                        ]
                );

                $connection->addColumn(
                        $setup->getTable('omnyfy_cms_country'), 'position_bottom', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Map Pin Position From Bottom',
                        ]
                );

                $connection->addColumn(
                        $setup->getTable('omnyfy_cms_country'), 'visitiors', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Visitors Count',
                        ]
                );
            }
        }

        if (version_compare($version, '3.0.8') < 0) {
            /**
             * Create table 'omnyfy_cms_industry'
             */
            $table = $setup->getConnection()->newTable(
                            $setup->getTable('omnyfy_cms_industry')
                    )->addColumn(
                            'id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true], 'ID'
                    )->addColumn(
                            'industry_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Industry Name'
                    )->addColumn(
                            'introduction', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 1024, ['nullable' => false], 'Industry Introduction'
                    )->addColumn(
                            'industry_category', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, NULL, ['nullable' => true, 'default' => null], 'Industry General Information'
                    )->addColumn(
                            'by_country', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, NULL, ['nullable' => true, 'default' => null], 'By Country'
                    )->addColumn(
                            'industry_profile_image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null], "Industry Profile image"
                    )->addColumn(
                            'background_image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], "Background Image"
                    )->addColumn(
                            'status', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 2, ['nullable' => false], '1=enable, 0 = disable'
                    )->addColumn(
                            'identifier', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true], "String Identifier"
                    )->addColumn(
                            'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'Created at timestamp'
                    )->addColumn(
                            'modified_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => true, 'default' => null], 'Modified at timestamp'
                    )->addForeignKey(
                            $setup->getFkName('omnyfy_cms_industry_by_country_industry_fk', 'by_country', 'omnyfy_cms_category', 'category_id'), 'by_country', $setup->getTable('omnyfy_cms_category'), 'category_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )->addForeignKey(
                            $setup->getFkName('omnyfy_cms_industry_general_info_category_cms_category_fk', 'industry_category', 'omnyfy_cms_category', 'category_id'), 'industry_category', $setup->getTable('omnyfy_cms_category'), 'category_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )->setComment(
                    'Omnyfy Cms Industry Table'
            );
            $setup->getConnection()->createTable($table);
        }



        if (version_compare($version, '3.0.9') < 0) {
            /**
             * Update table 'omnyfy_cms_tool_template'
             */
            // Get module table
            $tableName = $setup->getTable('omnyfy_cms_tool_template');

            // Check if the table already exists
            if ($connection->isTableExists($tableName) == true) {

                $connection->addColumn(
                        $setup->getTable('omnyfy_cms_tool_template'), 'type', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => '1: Tool, 2: Template',
                        ]
                );

                $connection->addColumn(
                        $setup->getTable('omnyfy_cms_tool_template'), 'icon', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Icon',
                        ]
                );

                $connection->addColumn(
                        $setup->getTable('omnyfy_cms_tool_template'), 'link', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Link',
                        ]
                );

                $connection->addColumn(
                        $setup->getTable('omnyfy_cms_tool_template'), 'upload_template', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Upload a template',
                        ]
                );

                $connection->addColumn(
                        $setup->getTable('omnyfy_cms_tool_template'), 'link_type', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 6,
                    'nullable' => true,
                    'comment' => ' 0: Document, 1: Url',
                        ]
                );

                $connection->addColumn(
                        $setup->getTable('omnyfy_cms_tool_template'), 'position', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Position',
                        ]
                );
            }
        }
        $setup->endSetup();
    }

}
