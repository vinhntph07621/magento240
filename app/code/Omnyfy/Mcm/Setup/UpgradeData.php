<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Omnyfy\Mcm\Setup;

use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Omnyfy\Mcm\Model\SequenceFactory;
use Omnyfy\Mcm\Model\ResourceModel\VendorBankAccountType;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface {

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory, SequenceFactory $sequenceFactory, VendorBankAccountType $vendorBankAccountType) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->sequenceFactory = $sequenceFactory;
        $this->vendorBankAccountType = $vendorBankAccountType;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->updateAttribute(
                Category::ENTITY, 'category_commission_percentage', [
            'note' => 'Set the commission rate that would like to charge Vendors for sales of products or services under this category. Do not enter the "%" sign simply enter the number (e.g. for 5% enter 5.00)'
                ]
        );
        $setup->startSetup();

        $version = $context->getVersion();
        if (version_compare($version, '1.0.6') < 0) {
            if ($setup->tableExists('omnyfy_mcm_sequence')) {
                $payoutSequence = $this->createSequence()->load('payout_ref', 'type');
                if (empty($payoutSequence->getData())) {
                    $sequenceData = [
                        'type' => 'payout_ref',
                        'prefix' => 'PR',
                        'last_value' => '0'
                    ];
                    $this->createSequence()->setData($sequenceData)->save();
                }
            }
        }
        if (version_compare($version, '1.1.0') < 0) {
            if ($setup->tableExists('omnyfy_mcm_vendor_bank_account_type')) {
                $bankAccTypeData = [
                    ['account_type' => 'Bank Account (Direct Deposit)'],
                    ['account_type' => 'International Bank Account (EFT)'],
                ];
                $this->vendorBankAccountType->insertMultiple('omnyfy_mcm_vendor_bank_account_type', $bankAccTypeData);
            }
        }
        
        if (version_compare($version, '1.1.6') < 0) {
            if ($setup->tableExists('omnyfy_mcm_sequence')) {
                $invoiceSequence = $this->createSequence()->load('invoice_ref', 'type');             
                if (empty($invoiceSequence->getData())) {
                    $sequenceData = [
                        'type' => 'invoice_ref',
                        'prefix' => 'INV',
                        'last_value' => '0'
                    ];
                    $this->createSequence()->setData($sequenceData)->save();
                }
            }
        }
        
        $setup->endSetup();
    }

    public function createSequence() {
        return $this->sequenceFactory->create();
    }
}
