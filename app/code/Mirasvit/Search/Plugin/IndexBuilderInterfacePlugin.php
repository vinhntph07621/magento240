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



namespace Mirasvit\Search\Plugin;

use Magento\Framework\Search\Adapter\Mysql\IndexBuilderInterface;
use Magento\Framework\DB\Select;

class IndexBuilderInterfacePlugin
{
    /**
     * @param \Magento\SharedCatalog\Plugin\Framework\Search\Adapter\Mysql\IndexBuilderInterfacePlugin $subject
     * @param callable $proceed
     * @param IndexBuilderInterface $builder
     * @param Select $select
     *
     * @return \Magento\Framework\DB\Select
     */
    public function aroundAfterBuild($subject, $proceed, IndexBuilderInterface $builder, Select $select)
    {
        if (strripos($select->__toString(), 'catalogsearch_fulltext') !== false) {
            return $proceed($builder, $select);
        }

        return $select;
    }
}
