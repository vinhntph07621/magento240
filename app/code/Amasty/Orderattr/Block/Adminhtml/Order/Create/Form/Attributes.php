<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


namespace Amasty\Orderattr\Block\Adminhtml\Order\Create\Form;

use Amasty\Orderattr\Model\ResourceModel\Attribute\CollectionFactory;
use Amasty\Orderattr\Api\Data\CheckoutAttributeInterface;
use Amasty\Orderattr\Model\ConfigProvider;
use Magento\Backend\Block\Template\Context;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

/**
 * This form used in Order Create / Order Edit / Order Reorder / Amasty Order Attribute Value Edit
 */
class Attributes extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_template = 'order/create/attributes_form.phtml';

    protected $quote;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var \Amasty\Orderattr\Model\ResourceModel\Attribute\Collection
     */
    private $attributesCollection;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        CollectionFactory $collectionFactory,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        $this->attributesCollection = $collectionFactory->create();
        $this->configProvider = $configProvider;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->attributesCollection->setSortOrder();
        $this->attributesCollection->addFieldToFilter(CheckoutAttributeInterface::IS_VISIBLE_ON_BACK, 1);

    }

    /**
     * @return \Magento\Quote\Model\Quote|\Magento\Sales\Model\Order
     */
    public function getQuote()
    {
        if (!$this->quote && $this->getParentBlock()) {
            return $this->getParentBlock()->getOrder();
        }
        return $this->quote;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'     => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                ]
            ]
        );

        $form->setUseContainer(true);

        $fieldset = $form->addFieldset('base_fieldset', ['collapsable' => false]);
        
        if ($this->getQuote()) {
            if ($this->getQuote()->getStoreId() !== null) {
                $this->attributesCollection->addStoreFilter($this->getQuote()->getStoreId());
            }

            if ($this->getQuote()->getCustomerGroupId() !== null) {
                $this->attributesCollection->addCustomerGroupFilter((int)$this->getQuote()->getCustomerGroupId());
            }
        }

        $orderAttributes = $this->attributesCollection->getItems();

        foreach ($orderAttributes as &$attribute) {
            if ($attribute->getFrontendInput() === 'html') {
                $attribute->setFrontendInput('text');
            }
        }

        $this->_setFieldset($orderAttributes, $fieldset);

        if ($this->getRequest()->getActionName() != 'edit') {
            $defaultValues = [];

            foreach ($orderAttributes as $attribute) {
                if ($defaultValue = $attribute->getDefaultValue()) {
                    $code = $attribute->getAttributeCode();
                    switch ($attribute->getFrontendInput()) {
                        case 'multiselect':
                        case 'checkboxes':
                            $defaultValues[$code] = explode(',', $defaultValue);
                            break;
                        default:
                            $defaultValues[$code] = $defaultValue;
                            break;
                    }
                }
            }

            if ($defaultValues) {
                $form->setValues($defaultValues);
            }
        }

        $form->addFieldNameSuffix('order[extension_attributes][amasty_order_attributes]');
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Initialize form fields values
     * Method will be called after prepareForm and can be used for field values initialization
     *
     * @return $this
     */
    protected function _initFormValues()
    {
        parent::_initFormValues();
        if ($this->getQuote() && $this->getQuote()->getExtensionAttributes()) {
            $attributes = $this->getQuote()->getExtensionAttributes()->getAmastyOrderAttributes();
            if (!empty($attributes)) {
                /** @var \Magento\Framework\Api\AttributeInterface $customAttribute */
                foreach ($attributes as $customAttribute) {
                    $element = $this->getForm()->getElement($customAttribute->getAttributeCode());
                    if ($element) {
                        $value = $customAttribute->getValue();
                        switch ($element->getEntityAttribute()->getFrontendInput()) {
                            case 'multiselect':
                            case 'checkboxes':
                                $value = explode(',', $value);
                                break;
                        }
                        $element->setValue($value);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getAdditionalElementTypes()
    {
        return [
            'datetime' => 'date',
            'boolean'  => 'select'
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function _applyTypeSpecificConfig($inputType, $element, Attribute $attribute)
    {
        switch ($inputType) {
            case 'boolean':
                $element->setValues($attribute->getSource()->getAllOptions());
                break;
            case 'select':
                $values = $attribute->getSource()->getAllOptions(false, true);
                array_unshift($values, ['label' => ' ', 'value' => '']);
                $element->setValues($values);
                break;
            case 'date':
                $element->addClass('date-calendar');
                $element->setDateFormat($this->configProvider->getDateFormatJs());
                break;
            case 'datetime':
                $element->addClass('date-calendar');
                $element->setDateFormat($this->configProvider->getDateFormatJs());
                $element->setTimeFormat($this->configProvider->getTimeFormatJs());
                break;
            case 'multiselect':
            case 'checkboxes':
                $attributeCode = $attribute->getAttributeCode();
                $element->setValues($attribute->getSource()->getAllOptions(false, true));
                $element->setName($attributeCode . '[]');
                break;
            case 'radios':
                $element->setValues($attribute->getSource()->getAllOptions(false, true));
                break;
            default:
                break;
        }
    }
}
