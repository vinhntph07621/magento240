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


class Productlist extends AbstractProduct
{
    /**
     * @inheritDoc
     */
    public function loadOperatorOptions()
    {
        parent::loadOperatorOptions();
        $this->setData('operator_option', [
            '==' => __('Found'),
            '!=' => __('Not Found'),
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __('If Product is %1 in the %2 with %3 of these Conditions match:',
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
        return __('If Product is %1 in the %2 with %3 of these Conditions match:',
            $this->getOperatorName(), $this->getValueName(), $this->getAggregatorName()
        );
    }
}