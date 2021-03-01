<?php
namespace Omnyfy\VendorSignUp\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface {

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;

        $installer->startSetup();
		
		/**
         * Create table 'omnyfy_vendor_signup'
         */
        $table = $installer->getConnection()
                ->newTable($installer->getTable('omnyfy_vendor_signup'))
                ->addColumn(
                        'id', Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Template ID'
                )->addColumn(
                        'business_name', Table::TYPE_TEXT, 255, ['nullable' => false], 'Business Name'
                )->addColumn(
						'first_name', Table::TYPE_TEXT, 80, ['nullable' => false, 'default' => ''], 'First Name'
				)->addColumn(
						'last_name', Table::TYPE_TEXT, 80, ['nullable' => false, 'default' => ''], 'Last Name'
				)->addColumn(
						'dob', Table::TYPE_DATE, null, [], 'Date of Birth'
				)->addColumn(
                        'business_address', Table::TYPE_TEXT, 255, ['nullable' => false], 'Business Address'
                )->addColumn(
                        'city', Table::TYPE_TEXT, 100, ['nullable' => false], 'City'
                )->addColumn(
                        'state', Table::TYPE_TEXT, 100, ['nullable' => false], 'State'
                )->addColumn(
                        'country', Table::TYPE_TEXT, 100, ['nullable' => false], 'Country'
                )->addColumn(
                        'postcode', Table::TYPE_TEXT, 15, ['nullable' => false], 'Postcode'
                )->addColumn(
                        'country_code', Table::TYPE_TEXT, 10, ['nullable' => false], 'Country Code'
                )->addColumn(
                        'telephone', Table::TYPE_TEXT, 10, ['nullable' => false], 'Telephone'
                )->addColumn(
                        'email', Table::TYPE_TEXT, 100, ['nullable' => false], 'Email'
                )->addColumn(
                        'legal_entity', Table::TYPE_TEXT, 255, ['nullable' => false], 'Legal Entity Name'
                )->addColumn(
                        'government_number', Table::TYPE_TEXT, 50, ['nullable' => false], 'Government Number'
                )->addColumn(
                        'tax_number', Table::TYPE_TEXT, 50, ['nullable' => false], 'Tax Number'
                )->addColumn(
                        'abn', Table::TYPE_TEXT, 50, ['nullable' => false], 'ABN'
                )->addColumn(
                        'description', Table::TYPE_TEXT, '64k', ['nullable' => false], 'Business Description'
                )->addColumn(
                        'created_by', Table::TYPE_TEXT, 10, ['nullable' => false], 'Created By'
                )->addColumn(
                        'status', Table::TYPE_SMALLINT, null, ['nullable' => false], 'Status'
                )->addColumn(
                        'created_at', Table::TYPE_TIMESTAMP, '', ['nullable' => false, 'default' => Table::TIMESTAMP_INIT], 'Creation Date'
                )->setComment(
					'Vendor Sign Up Details'
				);
        $installer->getConnection()->createTable($table);
		
		if (!$installer->tableExists('omnyfy_vendor_kyc_details')) {
			$kycDetailsTable = $installer->getConnection()->newTable(
				$installer->getTable('omnyfy_vendor_kyc_details')
					)
					->addColumn(
							'id', Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'ID'
					)->addColumn(
							'vendor_id', Table::TYPE_INTEGER, null, ['nullable' => false, 'default' => 0, 'unsigned' => true], 'Vendor ID'
					)->addColumn(
							'signup_id', Table::TYPE_INTEGER, null, ['nullable' => false, 'default' => 0, 'unsigned' => true], 'Vendor SignUp ID'
					)->addColumn(
							'kyc_status', Table::TYPE_TEXT, 50, ['nullable' => false], 'KYC Status'
					)->addColumn(
							'status_code', Table::TYPE_TEXT, 50, ['nullable' => false, 'default' => ''], 'Status Code'
					)->addColumn(
							'kyc_user_id', Table::TYPE_TEXT, 100, ['nullable' => false, 'default' => ''], 'KYC User Id'
					)->addColumn(
							'kyc_company_id', Table::TYPE_TEXT, 100, ['nullable' => false, 'default' => ''], 'KYC Company Id'
					)->addColumn(
                        'assembly_response', Table::TYPE_TEXT, '64k', ['nullable' => false], 'Assembly Pay Response'
					)->addColumn(
							'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Create date'
					)->addColumn(
							'updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Update date'
					)
                ->addIndex(
                    $setup->getIdxName(
                        $setup->getTable('omnyfy_vendor_kyc_details'),
                        ['vendor_id'],
                        AdapterInterface::INDEX_TYPE_INDEX
                    ),
                    ['vendor_id'],
                    AdapterInterface::INDEX_TYPE_INDEX
                )
                ->addIndex(
                    $setup->getIdxName(
                        $setup->getTable('omnyfy_vendor_kyc_details'),
                        ['signup_id'],
                        AdapterInterface::INDEX_TYPE_INDEX
                    ),
                    ['signup_id'],
                    AdapterInterface::INDEX_TYPE_INDEX
                )
                ->addForeignKey(
							$installer->getFkName(
							    'omnyfy_vendor_kyc_details',
                                'vendor_id',
                                'omnyfy_vendor_vendor_entity',
                                'entity_id'
                            ),
                            'vendor_id',
                            $installer->getTable('omnyfy_vendor_vendor_entity'),
                            'entity_id',
                            Table::ACTION_CASCADE
					)
            ;
			$installer->getConnection()->createTable($kycDetailsTable);
		}

		if ($installer->tableExists('omnyfy_vendor_kyc_details')) {
		    $tableName = $installer->getTable('omnyfy_vendor_kyc_details');
		    $fkName = $installer->getFkName(
                'omnyfy_vendor_kyc_details',
                'signup_id',
                'omnyfy_vendor_signup',
                'id'
            );
		    $keys = $installer->getConnection()->getForeignKeys($tableName);
		    $found = false;
		    foreach($keys as $key) {
		        if ($key['FK_NAME'] == $fkName) {
		            $found = true;
		            break;
                }
            }

		    if (!$found) {
                $installer->getConnection()
                    ->addForeignKey(
                        $fkName,
                        $installer->getTable('omnyfy_vendor_kyc_details'),
                        'signup_id',
                        $installer->getTable('omnyfy_vendor_signup'),
                        'id',
                        Table::ACTION_CASCADE
                    );
            }
        }
		
        $installer->endSetup();
    }

}
