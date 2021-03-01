<?php
namespace Omnyfy\Stripe\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Omnyfy\Vendor\Setup\VendorSetupFactory
     */
    protected $vendorSetupFactory;

    /**
     * UpgradeData constructor.
     * @param \Omnyfy\Vendor\Setup\VendorSetupFactory $vendorSetupFactory
     */
    public function __construct(
        \Omnyfy\Vendor\Setup\VendorSetupFactory $vendorSetupFactory
    )
    {
        $this->vendorSetupFactory = $vendorSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $version = $context->getVersion();
        if (version_compare($version, '1.0.4', '<')) {
            $vendorSetup = $this->vendorSetupFactory->create(['setup' => $setup]);
            $vendorEntity = \Omnyfy\Vendor\Model\Vendor::ENTITY;

            $vendorSetup->updateAttribute(
                $vendorEntity,
                'stripe_account_code',
                [
                    'required' => false,
                    'is_visible' => false
                ]
            );
        }
        $setup->endSetup();
    }
}
