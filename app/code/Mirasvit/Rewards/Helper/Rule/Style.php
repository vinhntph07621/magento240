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


namespace Mirasvit\Rewards\Helper\Rule;

use Mirasvit\Rewards\Api\Config\Rule\SpendingStyleInterface;

class Style extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @return array
     */
    public function getOptionsArray()
    {
        return [
            ['label' => __('Flexible'), 'value' => SpendingStyleInterface::STYLE_PARTIAL],
            ['label' => __('Fixed'), 'value' => SpendingStyleInterface::STYLE_FULL],
        ];
    }
}
