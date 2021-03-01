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
 * @package   mirasvit/module-search
 * @version   1.0.151
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Search\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var UpgradeDataInterface[]
     */
    private $pool;

    /**
     * UpgradeData constructor.
     * @param UpgradeData\UpgradeData103 $upgrade103
     * @param UpgradeData\UpgradeData104 $upgrade104
     * @param UpgradeData\UpgradeData107 $upgrade107
     * @param UpgradeData\UpgradeData1011 $upgrade1011
     */
    public function __construct(
        UpgradeData\UpgradeData103 $upgrade103,
        UpgradeData\UpgradeData104 $upgrade104,
        UpgradeData\UpgradeData107 $upgrade107,
        UpgradeData\UpgradeData1011 $upgrade1011
    ) {
        $this->pool = [
            '1.0.3' => $upgrade103,
            '1.0.4' => $upgrade104,
            '1.0.7' => $upgrade107,
            '1.0.11' => $upgrade1011,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        foreach ($this->pool as $version => $upgrade) {
            if (version_compare($context->getVersion(), $version) < 0) {
                $upgrade->upgrade($setup, $context);
            }
        }
        $setup->endSetup();
    }
}
