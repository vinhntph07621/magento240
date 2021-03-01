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



namespace Mirasvit\CustomerSegment\Model\Segment\Condition\Product;


class ItemHistory extends AbstractProduct
{
    /**
     * @inheritDoc
     */
    public function loadOperatorOptions()
    {
        parent::loadOperatorOptions();
        $this->setData('operator_option', [
            '==' => __('Was'),
            '!=' => __('Was Not'),
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __('If Item %1 %2 and matches %3 of these Conditions:',
                $this->getOperatorElementHtml(),
                $this->getValueElementHtml(),
                $this->getAggregatorElement()->toHtml()
            )
            . $this->getRemoveLinkHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function asString($format = '')
    {
        return __('If Item %1 %2 and matches %3 of these Conditions:',
            $this->getOperatorName(), $this->getValueName(), $this->getAggregatorName()
        );
    }
}