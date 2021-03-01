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



namespace Mirasvit\CustomerSegment\Model\Segment\Condition\Customer\Address;

use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;
use Magento\Directory\Model\Config\Source\Allregion;

class Attributes extends AbstractCondition
{
    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var Allregion
     */
    private $directoryAllregion;

    /**
     * Attributes constructor.
     *
     * @param Allregion                    $directoryAllregion
     * @param SearchCriteriaBuilder        $searchCriteriaBuilder
     * @param AttributeRepositoryInterface $attributeRepository
     * @param Context                      $context
     * @param array                        $data
     */
    public function __construct(
        Allregion $directoryAllregion,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        AttributeRepositoryInterface $attributeRepository,
        Context $context,
        array $data = []
    ) {
        $this->directoryAllregion = $directoryAllregion;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->attributeRepository = $attributeRepository;
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
        $addressAttributesList = $this->attributeRepository->getList(
            'customer_address',
            $this->searchCriteriaBuilder->create()
        );

        foreach ($addressAttributesList->getItems() as $attr) {
            $label = $attr->getDefaultFrontendLabel();
            if ($attr->getAttributeCode() == 'region') {
                $label = __('Region');
            }

            $attributes[$attr->getAttributeCode()] = $label;
        }

        $this->setData('attribute_option', $attributes);

        return $this;
    }

    /**
     * Retrieve attribute element
     *
     * @return AbstractCondition
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setData('show_as_text', true); // Do not allow to choose other elements from current optgroup

        return $element;
    }

    /**
     * Get current attribute object.
     *
     * @return \Magento\Eav\Api\Data\AttributeInterface|\Magento\Customer\Model\Attribute
     */
    private function getAttributeObject()
    {
        return $this->attributeRepository->get('customer_address', $this->getData('attribute'));
    }

    /**
     * @inheritDoc
     */
    public function getValueSelectOptions()
    {

        if (!$this->getData('value_select_options') && is_object($attr = $this->getAttributeObject())) {
            /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attr */
            if ($attr->usesSource()) {
                switch ($attr->getAttributeCode()) {
                    case 'region_id':
                        $options = $this->directoryAllregion->toOptionArray();
                        break;

                    default:
                        $options = $attr->getSource()->getAllOptions();
                }
                $this->setData('value_select_options', $options);
            }
        }

        return $this->getData('value_select_options');
    }

    /**
     * Get input type for attribute operators.
     *
     * @return string
     */
    public function getInputType()
    {
        if ($this->getData('attribute') == 'region_id') {
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
     *
     * @return string
     */
    public function getValueElementType()
    {
        if ($this->getData('attribute') == 'region_id') {
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
     *
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
     *
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
     * {@inheritDoc}
     */
    public function asHtml()
    {
        return __('Address: %1', parent::asHtml());
    }
}
