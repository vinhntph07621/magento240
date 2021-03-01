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



namespace Mirasvit\CustomerSegment\Model\Segment\Condition\Item;


use Magento\Rule\Model\Condition\AbstractCondition;

class Attributes extends AbstractCondition
{
    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption([
            'sku' => __('SKU')
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getNewChildSelectOptions()
    {
        $conditions = [];
        foreach ($this->loadAttributeOptions()->getData('attribute_option') as $code => $label) {
            $conditions[] = [
                'value' => $this->getData('type') . '|' . $code,
                'label' => $label
            ];
        }

        return [
            'value' => $conditions,
            'label' => $this->getData('label')
        ];
    }

    /**
     * @inheritDoc
     */
    public function asHtml()
    {
        return __('Item: %1', parent::asHtml());
    }
}