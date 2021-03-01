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


namespace Mirasvit\Rewards\Plugin;

use Magento\Tax\Model\Config;
use Magento\Store\Model\Store;

/**
 * @package Mirasvit\Rewards\Plugin
 */
class TaxConfig
{
    /**
     * @param Config    $config
     * @param \callable $proceed
     * @param Store     $store
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundNeedPriceConversion(Config $config, $proceed, $store = null)
    {
        //Doofinder\Feed\Model\Tax\Plugin\Config->aroundNeedPriceConversion doesn't pass $store object
        if ($store && $store->getCalculateRewardsTax()) {
            return true;
        }

        return $proceed($store);
    }
}