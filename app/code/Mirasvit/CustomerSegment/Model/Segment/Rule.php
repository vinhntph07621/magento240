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



namespace Mirasvit\CustomerSegment\Model\Segment;

use Magento\Framework\Data\FormFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Rule\Model\AbstractModel;

class Rule extends AbstractModel
{
    const FORM_NAME = 'customersegment_segment_form';

    /**
     * @var Condition\CombineFactory
     */
    private $conditionCombineFactory;

    /**
     * Rule constructor.
     * @param Condition\CombineFactory $conditionCombineFactory
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param TimezoneInterface $localeDate
     */
    public function __construct(
        Condition\CombineFactory $conditionCombineFactory,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate
    ) {
        $this->conditionCombineFactory = $conditionCombineFactory;

        parent::__construct($context, $registry, $formFactory, $localeDate);
    }

    /**
     * @return \Magento\Rule\Model\Action\Collection|void
     */
    public function getActionsInstance()
    {
    }

    /**
     * @return \Magento\Rule\Model\Condition\Combine|Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->conditionCombineFactory->create();
    }
}
