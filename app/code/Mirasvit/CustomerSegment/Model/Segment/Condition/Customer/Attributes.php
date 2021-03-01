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



namespace Mirasvit\CustomerSegment\Model\Segment\Condition\Customer;

use Magento\Customer\Model\CustomerFactory;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;

/**
 * Class allows to validate customers' attributes.
 */
class Attributes extends AbstractCondition
{
    const ADDRESS_EXISTS         = 1;
    const ADDRESS_DOES_NOT_EXIST = 0;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * Attributes constructor.
     *
     * @param AttributeRepositoryInterface $attributeRepository
     * @param CustomerFactory              $customerFactory
     * @param Context                      $context
     * @param array                        $data
     */
    public function __construct(
        AttributeRepositoryInterface $attributeRepository,
        CustomerFactory $customerFactory,
        Context $context,
        array $data = []
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->customerFactory     = $customerFactory;
        parent::__construct($context, $data);
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
                'label' => $label,
            ];
        }

        return $conditions;
    }

    /**
     * {@inheritdoc}
     */
    public function loadAttributeOptions()
    {
        $attributes = [];
        /** @var \Magento\Customer\Model\Attribute[] $customerAttributes */
        $customerAttributes = $this->customerFactory->create()->getAttributes();
        foreach ($customerAttributes as $attr) {
            if ($attr->getStoreLabel() && $attr->getAttributeCode()) {
                $attributes[$attr->getAttributeCode()] = $attr->getStoreLabel();
            }
        }

        $this->setData('attribute_option', $attributes);

        return $this;
    }

    /**
     * Check if current condition attribute is default billing or shipping address
     * @return bool
     */
    protected function _isCurrentAttributeDefaultAddress()
    {
        $code = $this->getAttributeObject()->getAttributeCode();

        return $code == 'default_billing' || $code == 'default_shipping';
    }

    /**
     * Get current attribute object.
     * @return \Magento\Eav\Api\Data\AttributeInterface|\Magento\Customer\Model\Attribute
     */
    private function getAttributeObject()
    {
        return $this->attributeRepository->get('customer', $this->getData('attribute'));
    }

    /**
     * @inheritDoc
     */
    public function getValueSelectOptions()
    {
        if (!$this->getData('value_select_options') && is_object($attr = $this->getAttributeObject())) {
            /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attr */
            if ($attr->usesSource()) {
                $options = $attr->getSource()->getAllOptions();
                $this->setData('value_select_options', $options);
            }

            if ($this->_isCurrentAttributeDefaultAddress()) {
                $optionsArr = $this->_getOptionsForAttributeDefaultAddress();
                $this->setData('value_select_options', $optionsArr);
            }
        }

        return $this->getData('value_select_options');
    }

    /**
     * Get input type for attribute operators.
     * @return string
     */
    public function getInputType()
    {
        if ($this->_isCurrentAttributeDefaultAddress()) {
            return 'select';
        }

        if (!is_object($this->getAttributeObject())) {
            return 'string';
        }

        $input = $this->getAttributeObject()->getFrontendInput();
        switch ($input) {
            case 'boolean':
                return 'select';
            case 'select':
            case 'multiselect':
            case 'date':
                return $input;
            default:
                return 'string';
        }
    }

    /**
     * Get attribute value input element type
     * @return string
     */
    public function getValueElementType()
    {
        if ($this->_isCurrentAttributeDefaultAddress()) {
            return 'select';
        }

        if (!is_object($this->getAttributeObject())) {
            return 'text';
        }

        $input = $this->getAttributeObject()->getFrontendInput();
        switch ($input) {
            case 'boolean':
                return 'select';
            case 'select':
            case 'multiselect':
            case 'date':
                return $input;
            default:
                return 'text';
        }
    }

    /**
     * Retrieve value element
     * @return AbstractCondition
     */
    public function getValueElement()
    {
        $element = parent::getValueElement();
        if (is_object($attr = $this->getAttributeObject())) {
            switch ($attr->getFrontendInput()) {
                case 'date':
                    $element->setData('image', $this->_assetRepo->getUrl('Magento_Theme::calendar.png'));
                    break;
            }
        }

        return $element;
    }

    /**
     * Chechk if attribute value should be explicit
     * @return bool
     */
    public function getExplicitApply()
    {
        if (is_object($attr = $this->getAttributeObject())) {
            switch ($attr->getFrontendInput()) {
                case 'date':
                    return true;
            }
        }

        return false;
    }

    /**
     * Retrieve attribute element
     * @return AbstractCondition
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setData('show_as_text', true);

        return $element;
    }

    /**
     * Get options for customer default address attributes value select
     * @return array
     */
    protected function _getOptionsForAttributeDefaultAddress()
    {
        return [
            [
                'value' => self::ADDRESS_EXISTS,
                'label' => __('exists'),
            ],
            [
                'value' => self::ADDRESS_DOES_NOT_EXIST,
                'label' => __('does not exist'),
            ],
        ];
    }

    /**
     * Customer attributes are standalone conditions, hence they must be self-sufficient
     * @return string
     */
    public function asHtml()
    {
        return __('Customer %1', parent::asHtml());
    }

    /**
     * @inheritDoc
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        // validate address attributes
        if ($this->_isCurrentAttributeDefaultAddress() && !$model->hasData($this->getData('attribute'))) {
            $validatedValue = ($model->getData($this->getData('attribute')))
                ? self::ADDRESS_EXISTS
                : self::ADDRESS_DOES_NOT_EXIST;

            return $this->validateAttribute($validatedValue);
        }

        // validate custom attributes (custom eav attributes)
        $customAttributes = $model->getData('custom_attributes');
        if ($customAttributes && isset($customAttributes[$this->getData('attribute')])) {
            $attribute = $customAttributes[$this->getData('attribute')];
            if (isset($attribute['value'])) {
                return $this->validateAttribute($attribute['value']);
            }
        }

        return parent::validate($model);
    }
}
