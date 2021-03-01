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
 * @package   mirasvit/module-rewards
 * @version   3.0.21
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Rewards\Setup\UpgradeData;

use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Config;

class UpgradeData106 implements UpgradeDataInterface
{
    /**
     * @var Config
     */
    private $eavConfig;
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig       = $eavConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            Customer::ENTITY,
            'rewards_subscription',
            [
                'type'       => 'int',
                'label'      => 'Subscription to Points Expiring Notification',
                'input'      => 'select',
                'source'     => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required'   => true,
                'default'    => '1',
                'sort_order' => 200,
                'system'     => false,
                'position'   => 200
            ]
        );
        $subscription = $this->eavConfig->getAttribute(Customer::ENTITY, 'rewards_subscription');
        $subscription->setData(
            'used_in_forms',
            ['adminhtml_customer']
        );
        $subscription->getResource()->save($subscription);
    }
}
