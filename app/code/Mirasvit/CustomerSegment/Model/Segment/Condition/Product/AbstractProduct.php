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

use Magento\Rule\Model\Condition\Combine;
use Magento\Rule\Model\Condition\Context;
use Mirasvit\CustomerSegment\Api\Service\Segment\Condition\ValueProviderInterface;
use Mirasvit\CustomerSegment\Model\Segment\Condition\Daterange;

/**
 * @method \Magento\Rule\Model\Condition\AbstractCondition[] getConditionOptions() - Added through the di.xml
 */
class AbstractProduct extends Combine
{
    /**
     * @var string
     */
    protected $_inputType = 'select';

    /**
     * @var ValueProviderInterface[]
     */
    private $valueProviders;

    /**
     * Productlist constructor.
     *
     * @param ValueProviderInterface[] $valueProviders
     * @param Context                  $context
     * @param array                    $data
     */
    public function __construct(
        array $valueProviders = [],
        Context $context,
        array $data = []
    ) {
        $this->valueProviders = $valueProviders;
        parent::__construct($context, $data);
        $this->setData('type', get_class($this));
        $this->setData('value', $this->getData('default_value'));
    }

    /**
     * {@inheritdoc}
     */
    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();
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

        return $conditions;
    }

    /**
     * Add operator when loading array
     *
     * @param array $arr
     * @param string $key
     *
     * @return $this
     */
    public function loadArray($arr, $key = 'conditions')
    {
        if (isset($arr['operator'])) {
            $this->setData('operator', $arr['operator']);
        }

        if (isset($arr['attribute'])) {
            $this->setData('attribute', $arr['attribute']);
        }

        return parent::loadArray($arr, $key);
    }

    /**
     * @inheritdoc
     */
    public function loadValueOptions()
    {
        $values = [];
        foreach ($this->valueProviders as $valueProvider) {
            $values[$valueProvider->getCode()] = __($valueProvider->getLabel());
        }

        $this->setData('value_option', $values);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        \Magento\Framework\Profiler::start(__METHOD__);

        $result = false;
        $valueProvider = $this->getValueProvider($this->getValue());
        // Check whether the value provider can retrieve product collection for current candidate
        if (!$valueProvider->canProcessCandidate($model)) {
            return $result;
        }

        // We apply date range condition only to the provided collection, we mustn't apply it to products itself
        // so we temporary unset it from child conditions
        $dateRangeCondition = $this->getDateRangeCondition();
        $collection = $valueProvider->provideCollection($model, $dateRangeCondition);
        foreach ($collection as $product) {
            if (parent::validate($product)) {
                $result = true;
                break;
            }
        }

        // After validation set the daterange condition back to the child conditions
        // to be able to apply it to the next passed model
        if ($dateRangeCondition) {
            $this->setConditions(array_merge($this->getConditions(), [$dateRangeCondition]));
        }

        \Magento\Framework\Profiler::stop(__METHOD__);

        // Invert result if "Not Found" operator is used
        return ($this->getData('operator') == '==') ? $result : !$result;
    }

    /**
     * Get value provider fot given type.
     *
     * @param string $valueType
     *
     * @return ValueProviderInterface
     * @throws \InvalidArgumentException
     */
    private function getValueProvider($valueType)
    {
        $provider = null;
        foreach ($this->valueProviders as $valueProvider) {
            if ($valueProvider->getCode() == $valueType) {
                $provider = $valueProvider;
            }
        }

        if ($provider === null) {
            throw new \InvalidArgumentException(__('Value Provider for type %1 does not exist.', $valueType));
        }

        return $provider;
    }

    /**
     * Retrieve DateRange condition from child conditions and remove it.
     *
     * @return Daterange|null
     */
    private function getDateRangeCondition()
    {
        $conditions = [];
        $dateRange  = null;

        /** @var \Magento\Rule\Model\Condition\AbstractCondition $condition */
        foreach ($this->getConditions() as $condition) {
            if (stripos($condition->getData('type'), 'daterange') === false) {
                $conditions[] = $condition;
            } else {
                $dateRange = $condition;
            }
        }

        $this->setConditions($conditions);

        return $dateRange;
    }
}
