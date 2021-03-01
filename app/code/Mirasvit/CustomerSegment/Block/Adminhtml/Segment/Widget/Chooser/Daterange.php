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


namespace Mirasvit\CustomerSegment\Block\Adminhtml\Segment\Widget\Chooser;
use Magento\Backend\Block\AbstractBlock;
use Magento\Framework\Stdlib\DateTime;
class Daterange extends AbstractBlock
{
    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    private $formFactory;
    /**
     * HTML ID of the element that will obtain the joined chosen values
     *
     * @var string
     */
    protected $_targetElementId = '';
    /**
     * Range string delimiter for from/to dates
     *
     * @var string
     */
    protected $_rangeDelimiter = '...';
    /**
     * From/To values to be rendered
     *
     * @var array
     */
    protected $_rangeValues = array('from' => '', 'to' => '');
    /**
     * Daterange constructor.
     *
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Backend\Block\Context      $context
     * @param array                               $data
     */
    public function __construct(
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Block\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->formFactory = $formFactory;
    }
    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        if (empty($this->_targetElementId)) {
            return '';
        }
        $form = $this->formFactory->create();
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        foreach (['from' => __('From'), 'to' => __('To')] as $key => $label) {
            $id = "{$key}_{$this->_targetElementId}";
            $form->addField($id, 'date', [
                //'input_format' => DateTime::DATE_INTERNAL_FORMAT,
                'date_format' => $dateFormat,
                'label' => $label,
                'name' => $key,
                'value' => $this->_rangeValues[$key],
                'onchange' => "dateTimeChoose()",
            ]);
        }
        return $form->toHtml() . "<script type=\"text/javascript\">
            dateTimeChoose = function() {
                $('{$this->_targetElementId}').value = $('from_{$this->_targetElementId}').value + '...' + $('to_{$this->_targetElementId}').value;
            };
            </script>";
    }
    /**
     * Target element ID setter.
     *
     * @param string $value
     * @return $this
     */
    public function setTargetElementId($value)
    {
        $this->_targetElementId = trim($value);
        return $this;
    }
    /**
     * Range values setter.
     *
     * @param string $from
     * @param string $to
     *
     * @return $this
     */
    public function setRangeValues($from, $to)
    {
        $this->_rangeValues = array('from' => $from, 'to' => $to);
        return $this;
    }
    /**
     * Range values setter, string implementation.
     * Automatically attempts to split the string by delimiter.
     *
     * @param string $delimitedString
     *
     * @return $this
     */
    public function setRangeValue($delimitedString)
    {
        $split = explode($this->_rangeDelimiter, $delimitedString, 2);
        $from = $split[0]; $to = '';
        if (isset($split[1])) {
            $to = $split[1];
        }
        return $this->setRangeValues($from, $to);
    }
}