<?php
namespace Omnyfy\Stripe\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var \Omnyfy\Vendor\Setup\VendorSetupFactory
     */
    protected $vendorSetupFactory;

    /**
     * InstallData constructor.
     * @param \Omnyfy\Vendor\Setup\VendorSetupFactory $vendorSetupFactory
     */
    public function __construct(\Omnyfy\Vendor\Setup\VendorSetupFactory $vendorSetupFactory)
    {
        $this->vendorSetupFactory = $vendorSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $vendorSetup = $this->vendorSetupFactory->create(['setup' => $setup]);
        $vendorEntity = \Omnyfy\Vendor\Model\Vendor::ENTITY;

        $vendorSetup->addAttribute(
            $vendorEntity,
            'stripe_account_code',
            [
                'type' => 'varchar',
                'label' => 'Stripe Account Code',
                'input' => 'text',
                'required' => true,
                'system' => false,
            ]
        );
    }
}
