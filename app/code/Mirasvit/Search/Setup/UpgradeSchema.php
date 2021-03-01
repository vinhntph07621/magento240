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
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var UpgradeSchemaInterface[]
     */
    private $pool;

    /**
     * UpgradeSchema constructor.
     * @param UpgradeSchema\UpgradeSchema101 $upgrade101
     * @param UpgradeSchema\UpgradeSchema103 $upgrade103
     * @param UpgradeSchema\UpgradeSchema105 $upgrade105
     * @param UpgradeSchema\UpgradeSchema108 $upgrade108
     * @param UpgradeSchema\UpgradeSchema1010 $upgrade1010
     */
    public function __construct(
        UpgradeSchema\UpgradeSchema101 $upgrade101,
        UpgradeSchema\UpgradeSchema103 $upgrade103,
        UpgradeSchema\UpgradeSchema105 $upgrade105,
        UpgradeSchema\UpgradeSchema108 $upgrade108,
        UpgradeSchema\UpgradeSchema1010 $upgrade1010
    ) {
        $this->pool = [
            '1.0.1'  => $upgrade101,
            '1.0.3'  => $upgrade103,
            '1.0.5'  => $upgrade105,
            '1.0.8'  => $upgrade108,
            '1.0.10' => $upgrade1010,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
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
