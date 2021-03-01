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
 * @package   mirasvit/module-customer-segment
 * @version   1.0.51
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CustomerSegment\Model\Segment\Condition;

use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;

/**
 * Class adds outermost conditions to segment rules, conditions defined in di.xml.
 * @method \Magento\Rule\Model\Condition\AbstractCondition[] getConditionOptions() - Added through the di.xml
 */
class ConditionFactory extends AbstractCondition
{
    /**
     * ConditionFactory constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }

    /**
     * Retrieve child select conditions.
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = [];
        foreach ($this->getConditionOptions() as $condition) {
            if ($condition->getData('is_show_child_conditions')) {
                // Display child options immediately
                $conditions = array_merge($conditions, $condition->getNewChildSelectOptions());
            } else {
                // Load child options only after selecting this option
                $conditions[] = [
                    'value' => $condition->getData('type'),
                    'label' => $condition->getData('label'),
                ];
            }
        }

        return [
            'value' => $conditions,
            'label' => $this->getData('label'),
        ];
    }
}
