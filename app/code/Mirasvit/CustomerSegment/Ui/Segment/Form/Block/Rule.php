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



namespace Mirasvit\CustomerSegment\Ui\Segment\Form\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Rule\Block\Conditions;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;

class Rule extends Form
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var Form\Renderer\Fieldset
     */
    private $fieldsetRenderer;

    /**
     * @var Conditions
     */
    private $conditions;

    /**
     * @var string
     */
    protected $_nameInLayout = 'conditions_serialized';

    public function __construct(
        Conditions $conditions,
        Form\Renderer\Fieldset $fieldsetRenderer,
        FormFactory $formFactory,
        Registry $registry,
        Context $context,
        array $data = []
    ) {
        $this->registry         = $registry;
        $this->formFactory      = $formFactory;
        $this->fieldsetRenderer = $fieldsetRenderer;
        $this->conditions       = $conditions;

        parent::__construct($context, $data);
    }

    /**
     * @return Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $formName = \Mirasvit\CustomerSegment\Model\Segment\Rule::FORM_NAME;

        /** @var SegmentInterface $model */
        $model = $this->registry->registry(SegmentInterface::class);
        $rule  = $model->getRule();

        $form = $this->formFactory->create();
        $form->setData('html_id_prefix', 'rule_');

        $fieldsetName = $formName;

        $renderer = $this->fieldsetRenderer
            ->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setData('new_child_url', $this->getUrl('*/*/newConditionHtml', [
                'form'      => 'rule_' . $formName,
                'form_name' => $formName,
            ]));

        $fieldset = $form->addFieldset($fieldsetName, [
            'legend' => __('Segment customers based on the following conditions'),
        ])->setRenderer($renderer);

        $rule->getConditions()
            ->setFormName($formName);

        $conditionsField = $fieldset->addField('conditions', 'text', [
            'name'           => 'conditions',
            'required'       => true,
            'data-form-part' => $formName,
        ]);

        $conditionsField->setRule($rule)
            ->setRenderer($this->conditions)
            ->setFormName($formName);

        $form->setValues($model->getData());
        $this->setConditionFormName($rule->getConditions(), $formName);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param object $conditions
     * @param string $formName
     * @return void
     */
    private function setConditionFormName($conditions, $formName)
    {
        $conditions->setFormName($formName);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName);
            }
        }
    }
}
