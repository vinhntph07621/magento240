<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-01
 * Time: 17:52
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Location\Attribute\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Config\Model\Config\Source\Yesno;
use Omnyfy\Vendor\Model\Entity\Location\Attribute;
use Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

class Front extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var Yesno
     */
    protected $_yesNo;

    /**
     * @var PropertyLocker
     */
    private $propertyLocker;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Yesno $yesNo
     * @param PropertyLocker $propertyLocker
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Yesno $yesNo,
        PropertyLocker $propertyLocker,
        array $data = []
    ) {
        $this->_yesNo = $yesNo;
        $this->propertyLocker = $propertyLocker;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /** @var Attribute $attributeObject */
        $attributeObject = $this->_coreRegistry->registry('entity_attribute');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $yesnoSource = $this->_yesNo->toOptionArray();

        $fieldset = $form->addFieldset(
            'front_fieldset',
            ['legend' => __('Storefront Properties'), 'collapsable' => $this->getRequest()->has('popup')]
        );

        $fieldset->addField(
            'is_searchable',
            'select',
            [
                'name'     => 'is_searchable',
                'label'    => __('Use in Search'),
                'title'    => __('Use in Search'),
                'values'   => $yesnoSource,
            ]
        );

        $fieldset->addField(
            'is_visible_in_advanced_search',
            'select',
            [
                'name' => 'is_visible_in_advanced_search',
                'label' => __('Visible in Advanced Search'),
                'title' => __('Visible in Advanced Search'),
                'values' => $yesnoSource,
            ]
        );

        $this->_eventManager->dispatch('location_attribute_form_build_front_tab', ['form' => $form]);

        $fieldset->addField(
            'is_filterable',
            'select',
            [
                'name' => 'is_filterable',
                'label' => __("Use in Layered Navigation"),
                'title' => __('Can be used only with input type Dropdown, Multiple Select and Price'),
                'note' => __('Can be used only with input type Dropdown, Multiple Select and Price.'),
                'values' => [
                    ['value' => '0', 'label' => __('No')],
                    ['value' => '1', 'label' => __('Filterable (with results)')],
                    ['value' => '2', 'label' => __('Filterable (no results)')],
                ],
            ]
        );

        $fieldset->addField(
            'is_filterable_in_search',
            'select',
            [
                'name' => 'is_filterable_in_search',
                'label' => __("Use in Search Results Layered Navigation"),
                'title' => __('Can be used only with input type Dropdown, Multiple Select and Price'),
                'note' => __('Can be used only with input type Dropdown, Multiple Select and Price.'),
                'values' => $yesnoSource,
            ]
        );

        $fieldset->addField(
            'is_wysiwyg_enabled',
            'select',
            [
                'name' => 'is_wysiwyg_enabled',
                'label' => __('Enable WYSIWYG'),
                'title' => __('Enable WYSIWYG'),
                'values' => $yesnoSource,
            ]
        );

        $fieldset->addField(
            'is_html_allowed_on_front',
            'select',
            [
                'name' => 'is_html_allowed_on_front',
                'label' => __('Allow HTML Tags on Storefront'),
                'title' => __('Allow HTML Tags on Storefront'),
                'values' => $yesnoSource,
            ]
        );
        if (!$attributeObject->getId() || $attributeObject->getIsWysiwygEnabled()) {
            $attributeObject->setIsHtmlAllowedOnFront(1);
        }

        $fieldset->addField(
            'is_visible_on_front',
            'select',
            [
                'name' => 'is_visible_on_front',
                'label' => __('Visible on Catalog Pages on Storefront'),
                'title' => __('Visible on Catalog Pages on Storefront'),
                'values' => $yesnoSource
            ]
        );

        $fieldset->addField(
            'used_in_listing',
            'select',
            [
                'name' => 'used_in_listing',
                'label' => __('Used in Listing'),
                'title' => __('Used in Listing'),
                'note' => __('Depends on design theme.'),
                'values' => $yesnoSource
            ]
        );

        $fieldset->addField(
            'used_for_sort_by',
            'select',
            [
                'name' => 'used_for_sort_by',
                'label' => __('Used for Sorting in Listing'),
                'title' => __('Used for Sorting in Listing'),
                'note' => __('Depends on design theme.'),
                'values' => $yesnoSource
            ]
        );

        $fieldset->addField(
            'tooltip',
            'text',
            [
                'name' => 'tooltip',
                'label' => __('Tooltip'),
                'title' => __('Tooltip')
            ]
        );

        $this->_eventManager->dispatch(
            'adminhtml_vendor_attribute_edit_frontend_prepare_form',
            ['form' => $form, 'attribute' => $attributeObject]
        );

        // define field dependencies
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )->addFieldMap(
                "is_wysiwyg_enabled",
                'wysiwyg_enabled'
            )->addFieldMap(
                "is_html_allowed_on_front",
                'html_allowed_on_front'
            )->addFieldMap(
                "frontend_input",
                'frontend_input_type'
            )->addFieldDependence(
                'wysiwyg_enabled',
                'frontend_input_type',
                'textarea'
            )->addFieldDependence(
                'html_allowed_on_front',
                'wysiwyg_enabled',
                '0'
            )
                ->addFieldMap(
                    "is_searchable",
                    'searchable'
                )
                ->addFieldMap(
                    "is_visible_in_advanced_search",
                    'advanced_search'
                )
                ->addFieldDependence(
                    'advanced_search',
                    'searchable',
                    '1'
                )
        );

        $this->setForm($form);
        $form->setValues($attributeObject->getData());
        $this->propertyLocker->lock($form);
        return parent::_prepareForm();
    }
}
 