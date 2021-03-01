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

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\UrlInterface;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;

/**
 * Class validates created at date of a passed model within range of from...to dates
 */
class Daterange extends AbstractCondition
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Value form element
     *
     * @var mixed
     */
    private $_valueElement = null;

    /**
     * Input type for operator options
     *
     * @var string
     */
    protected $_inputType = 'select';

    /**
     * Parse condition value
     */
    private $valueParsed = null;

    /**
     * Daterange constructor.
     *
     * @param UrlInterface                $urlBuilder
     * @param Context                     $context
     * @param array                       $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Context $context,
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $data);
    }

    /**
     * Value element type getter
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Enable chooser selection button
     *
     * @return bool
     */
    public function getExplicitApply()
    {
        return true;
    }

    /**
     * Avoid value distortion by possible options
     *
     * @return array
     */
    public function getValueSelectOptions()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function asHtml()
    {
        $this->_valueElement = $this->getValueElement();

        return $this->getTypeElementHtml()
        . __('Date Range %1 within %2', $this->getOperatorElementHtml(), $this->_valueElement->getHtml())
        . $this->getRemoveLinkHtml()
        . $this->getChooserContainerHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function asString($format = '')
    {
        return __('Date Range %1 within %2', $this->getOperatorName(), $this->getValueName());
    }

    /**
     * Chooser button HTML getter
     *
     * @return string
     */
    public function getValueAfterElementHtml()
    {
        return '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="'
        . $this->_assetRepo->getUrl('images/rule_chooser_trigger.gif')
        . '" alt="" class="v-middle rule-chooser-trigger" '
        . 'title="' . __('Open Chooser') . '" /></a>';
    }

    /**
     * Chooser URL getter
     *
     * @return string
     */
    public function getValueElementChooserUrl()
    {
        return $this->urlBuilder->getUrl('customersegment/segment/chooserDaterange', [
            'value_element_id' => $this->_valueElement->getId(),
        ]);
    }

    /**
     * Parse condition value and return it.
     *
     * return array|false1
     * @return array|bool|null
     */
    public function getParsedValue()
    {
        if (null === $this->valueParsed) {
            $value = explode('...', $this->getValue());
            if (!isset($value[0]) || !isset($value[1])) {
                return false;
            }

            $regexp = '#^\d{2}/\d{1,2}/\d{4}$#';
            if (!preg_match($regexp, $value[0]) || !preg_match($regexp, $value[1])) {
                return false;
            }

            $start = strtotime($value[0]);
            $end = strtotime($value[1]);
            if (!$start || !$end) {
                return false;
            }

            $this->valueParsed = [$start, $end];
        }

        return $this->valueParsed;
    }

    /**
     * {@inheritDoc}
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        \Magento\Framework\Profiler::start(__METHOD__);
        $value = $this->getParsedValue();
        if (!$value) {
            return false;
        }

        list($start, $end) = $value;

        $createdAt = strtotime($model->getData('created_at'));
        if ($this->getData('operator') == '==') {
            $result = $createdAt >= $start && $createdAt <= $end;
        } else {
            $result = $createdAt < $start || $createdAt > $end;
        }

        \Magento\Framework\Profiler::stop(__METHOD__);

        return $result;
    }

    /**
     * Limit query by date range on provided field.
     *
     * @param \Magento\Framework\DB\Select $select
     * @param string $field
     *
     * @return $this
     */
    public function limitByDateRange(\Magento\Framework\DB\Select $select, $field)
    {
        if (!$this->getParsedValue()) {
            return $this;
        }

        list($start, $end) = $this->getParsedValue();
        $start = (new \DateTime())->setTimestamp($start)->format(DateTime::DATETIME_PHP_FORMAT);
        $end = (new \DateTime())->setTimestamp($end)->format(DateTime::DATETIME_PHP_FORMAT);
        $operator = ($this->getData('operator') == '==') ? 'BETWEEN' : 'NOT BETWEEN';

        $select->where("{$field} {$operator} '{$start}' AND '{$end}'");

        return $this;
    }
}
