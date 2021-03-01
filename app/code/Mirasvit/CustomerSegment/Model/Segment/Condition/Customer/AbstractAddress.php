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



namespace Mirasvit\CustomerSegment\Model\Segment\Condition\Customer;

use Magento\Rule\Model\Condition\Combine;

/**
 * @method \Magento\Rule\Model\Condition\AbstractCondition[] getConditionOptions() - Added through the di.xml
 */
abstract class AbstractAddress extends Combine
{
    const TYPE_ANY = 'any';

    /**
     * Retrieve condition label.
     *
     * @return string
     */
    abstract public function getConditionLabel();

    /**
     * {@inheritdoc}
     */
    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();
        foreach ($this->getConditionOptions() as $condition) {
            $conditions = array_merge($conditions, $condition->getNewChildSelectOptions());
        }

        return $conditions;
    }

    /**
     * Get type for value element.
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * Init list of available values
     *
     * @return AbstractAddress
     */
    public function loadValueOptions()
    {
        $this->setData('value_option', [
            \Magento\Sales\Model\Order\Address::TYPE_BILLING  => __('Billing'),
            \Magento\Sales\Model\Order\Address::TYPE_SHIPPING => __('Shipping'),
            self::TYPE_ANY =>                                    __('Any'),
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
        . __(
            'If %1 %2 Address match %3 of these Conditions:',
            $this->getConditionLabel(),
            $this->getValueElementHtml(),
            $this->getAggregatorElement()->getHtml()
        )
        . $this->getRemoveLinkHtml();
    }
}
