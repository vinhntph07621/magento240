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



namespace Mirasvit\CustomerSegment\Model\Segment\Condition\Order\Address;

use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;

/**
 * Class is not used anywhere.
 */
class Type extends AbstractCondition
{
    /**
     * @var string
     */
    protected $_inputType = 'boolean';

    /**
     * Type constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getNewChildSelectOptions()
    {
        return [
            [
                'value' => $this->getData('type'),
                'label' => $this->getData('label')
            ]
        ];
    }

    /**
     * Init list of available values
     *
     * @return Type
     */
    public function loadValueOptions()
    {
        $this->setValueOption([
            \Magento\Sales\Model\Order\Address::TYPE_BILLING  => __('Billing'),
            \Magento\Sales\Model\Order\Address::TYPE_SHIPPING => __('Shipping'),
        ]);

        return $this;
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
        . __('Order Address %1 a %2', $this->getOperatorElementHtml(), $this->getValueElementHtml())
        . $this->getRemoveLinkHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function asString($format = '')
    {
        return __(
            'Order Address %1 a %2',
            $this->getOperatorName(),
            $this->getValueName()
        );
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
}
