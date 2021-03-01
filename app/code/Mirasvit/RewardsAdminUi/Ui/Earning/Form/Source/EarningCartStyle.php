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


namespace Mirasvit\RewardsAdminUi\Ui\Earning\Form\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Mirasvit\RewardsAdminUi\Model\System\Source\Cartearningstyle;

class EarningCartStyle extends EarningStyle implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;
    protected $systemSourceCartearningstyle;

    public function __construct(
        Cartearningstyle $systemSourceCartearningstyle
    ) {
        $this->systemSourceCartearningstyle = $systemSourceCartearningstyle;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $sourceArray = $this->systemSourceCartearningstyle->toArray();
        $this->options = $this->toEarningOptionArray($sourceArray);

        return $this->options;
    }
}
