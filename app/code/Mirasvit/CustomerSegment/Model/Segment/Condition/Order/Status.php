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



namespace Mirasvit\CustomerSegment\Model\Segment\Condition\Order;

use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;
use Magento\Sales\Model\Order\Config as OrderConfig;

class Status extends AbstractCondition
{
    /**
     * @var OrderConfig
     */
    private $orderConfig;

    /**
     * Status constructor.
     *
     * @param OrderConfig $orderConfig
     * @param Context     $context
     * @param array       $data
     */
    public function __construct(
        OrderConfig $orderConfig,
        Context $context,
        array $data = []
    ) {
        $this->orderConfig = $orderConfig;
        parent::__construct($context, $data);
    }

    /**
     * Init list of available values
     *
     * @return Status
     */
    public function loadValueOptions()
    {
        $this->setValueOption($this->orderConfig->getStatuses());

        return $this;
    }

    /**
     * Get type for value element.
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'multiselect';
    }

    /**
     * @inheritDoc
     */
    public function getInputType()
    {
        return 'grid';
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
        . __('Order Status %1 %2', $this->getOperatorElementHtml(), $this->getValueElementHtml())
        . $this->getRemoveLinkHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function asString($format = '')
    {
        return __(
            'Order Status %1 %2',
            $this->getOperatorName(),
            $this->getValueName()
        );
    }

    /**
     * @inheritDoc
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        return parent::validateAttribute($model->getData('status'));
    }

    /**
     * Retrieve parsed value.
     * Base method incorrectly retrieves array value.
     *
     * {@inheritdoc}
     */
    public function getValueParsed()
    {
        if (!$this->hasValueParsed()) {
            $value = $this->getData('value');
            if ($this->isArrayOperatorType() && is_string($value)) {
                $value = preg_split('#\s*[,;]\s*#', $value, null, PREG_SPLIT_NO_EMPTY);
            }
            $this->setValueParsed($value);
        }
        return $this->getData('value_parsed');
    }
}
