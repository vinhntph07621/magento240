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

use Magento\Rule\Model\Condition\Combine;
use Magento\Rule\Model\Condition\Context;

abstract class AbstractCombineCondition extends Combine
{
    /**
     * AbstractCombineCondition constructor.
     * @param Context $context
     * @param array $data
     * @throws \Exception
     */
    public function __construct(Context $context, array $data = [])
    {
        $data['type'] = get_class($this);

        if (!isset($data['condition_options'])) {
            throw new \Exception('condition_options is empty');
        }

        parent::__construct($context, $data);

        $this->setType($data['type']);
    }

    /**
     * @return AbstractCondition[]
     */
    public function getConditionOptions()
    {
        return $this->getData('condition_options');
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getData('type');
    }

    /**
     * {@inheritdoc}
     */
    public function getNewChildSelectOptions()
    {
        $conditions = [
            [
                'value' => $this->getType(),
                'label' => __('Conditions Combination'),
            ],
        ];

        // Collect all custom conditions
        foreach ($this->getConditionOptions() as $condition) {
            if ($condition->getData('is_show_child_conditions')) {
                $conditions[] = $condition->getNewChildSelectOptions();
            } else {
                $conditions[] = [
                    'value' => $condition->getData('type'),
                    'label' => $condition->getData('label'),
                ];
            }
        }

        $conditions = array_merge_recursive(parent::getNewChildSelectOptions(), $conditions);

        return $conditions;
    }
}
