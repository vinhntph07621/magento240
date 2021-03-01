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
 * @package   mirasvit/module-rma
 * @version   2.1.18
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rma\Helper;

class Rule extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $productFactory;
    /**
     * @var \Magento\Eav\Model\Entity\AttributeFactory
     */
    private $entityAttributeFactory;
    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    private $context;

    /**
     * Rule constructor.
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Eav\Model\Entity\AttributeFactory $entityAttributeFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Eav\Model\Entity\AttributeFactory $entityAttributeFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->productFactory         = $productFactory;
        $this->entityAttributeFactory = $entityAttributeFactory;
        $this->objectManager          = $objectManager;
        $this->context                = $context;

        parent::__construct($context);
    }

    /**
     * @var array
     */
    protected $operatorInputByType = [
        'string'      => ['==', '!=', '>=', '>', '<=', '<', '{}', '!{}'],
        'numeric'     => ['==', '!=', '>=', '>', '<=', '<'],
        'date'        => ['==', '>=', '<='],
        'select'      => ['==', '!='],
        'boolean'     => ['==', '!='],
        'multiselect' => ['{}', '!{}', '()', '!()'],
        'grid'        => ['()', '!()'],
    ];

    /**
     * @var array
     */
    protected $operatorOptions = [
        '=='  => 'is',
        '!='  => 'is not',
        '>='  => 'equals or greater than',
        '<='  => 'equals or less than',
        '>'   => 'greater than',
        '<'   => 'less than',
        '{}'  => 'contains',
        '!{}' => 'does not contain',
        '()'  => 'is one of',
        '!()' => 'is not one of',
    ];

    /**
     * @param string $name
     * @param string $current
     * @param string $style
     * @param null   $tags
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getAttributeSelectHtml($name, $current, $style, $tags = null)
    {
        $options = [];
        $options['-'][] = '<option value="">'.__('not set').'</option>';

        $id = preg_replace('/[^a-zA-z_]/', '_', $name);

        $html = '<select name="'.$name.'" id="'.$id.'" style="'.$style.'" '.$tags.'>';
        foreach ($options as $group => $items) {
            if ($group == '-') {
                $html .= implode('', $items);
            } else {
                $html .= '<optgroup label="'.$group.'">';
                $html .= implode('', $items);
                $html .= '</optgroup>';
            }
        }

        $html .= '</select>';

        return $html;
    }

    /**
     * @param string $name
     * @param null   $current
     * @param null   $attributeCode
     *
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getConditionSelectHtml($name, $current = null, $attributeCode = null)
    {
        $conditions = [];

        if ($attributeCode != null) {
            $entityTypeId = $this->productFactory->create()->getResource()->getTypeId();
            $attribute = $this->entityAttributeFactory->create()->loadByCode($entityTypeId, $attributeCode);
            $type = 'string';
            if ($attributeCode === 'attribute_set_id') {
                $type = 'select';
            } elseif ($attributeCode === 'tracker') {
                $type = 'numeric';
            } else {
                switch ($attribute->getFrontendInput()) {
                    case 'select':
                        $type = 'select';
                        break;

                    case 'multiselect':
                        $type = 'multiselect';
                        break;

                    case 'date':
                        $type = 'date';
                        break;

                    case 'boolean':
                        $type = 'boolean';
                        break;

                    default:
                        $type = 'string';
                }
            }

            foreach ($this->operatorInputByType[$type] as $operator) {
                $operatorTitle = __($this->operatorOptions[$operator]);
                $selected = $current == $operator ? 'selected="selected"' : '';
                $conditions[] = '<option '.$selected.' value="'.$operator.'">'.$operatorTitle.'</option>';
            }
        }

        return '<select style="width:100px" name="'.$name.'">'.implode('', $conditions).'</select>';
    }

    /**
     * @param string $name
     * @param string $current
     * @param null   $tags
     *
     * @return string
     */
    public function getOutputTypeHtml($name, $current, $tags = null)
    {
        $element = $this->objectManager->create('Magento\Framework\Data\Form\Element\Select');
        $element
            ->setForm(new \Magento\Framework\DataObject())
            ->setValue($current)
            ->setName($name)
            ->addData($tags)
            ->setValues([
                'pattern' => __('Pattern'),
                'attribute' => __('Attribute Value'),
            ]);

        return $element->getElementHtml();
    }

    /**
     * @param string $name
     * @param null   $current
     * @param null   $attribute
     * @param null   $tags
     *
     * @return string
     */
    public function getAttributeValueHtml($name, $current = null, $attribute = null, $tags = null)
    {
        $html = '';

        $attribute = $this->productFactory->create()->getResource()->getAttribute($attribute);
        if ($attribute) {
            if ($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'multiselect') {
                $options = [];

                foreach ($attribute->getSource()->getAllOptions() as $option) {
                    $selected = '';
                    if ($option['value'] == $current) {
                        $selected = 'selected="selected"';
                    }
                    $options[] = '<option value="'.$option['value'].'" '.$selected.'>'.$option['label'].'</option>';
                }

                $html = '<select style="width:250px" name="'.$name.'" '.$tags.'>';
                $html .= implode('', $options);
                $html .= '</select>';
            }
        }

        if (!$html) {
            $html = '<input style="width:244px" class="input-text" type="text" name="'.$name.'" value="'.$current.'">';
        }

        return $html;
    }

    /**
     * @param string $name
     * @param null   $value
     *
     * @return string
     */
    public function getFormattersHtml($name, $value = null)
    {
        $element = $this->objectManager->create('Magento\Framework\Data\Form\Element\Select');
        $element
            ->setForm(new \Magento\Framework\DataObject())
            ->setValue($value)
            ->setName($name)
            ->setValues([
                '' => __('Default'),
                'intval' => __('Integer'),
                'price' => __('Price'),
                'strip_tags' => __('Strip Tags'),
            ]);

        return $element->getElementHtml();
    }

    /**
     * @param string $attributeCode
     *
     * @return \Magento\Framework\Phrase|string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getAttributeGroup($attributeCode)
    {
        $group = '';

        $primary = [
            'attribute_set',
            'attribute_set_id',
            'entity_id',
            'full_description',
            'meta_description',
            'meta_keyword',
            'meta_title',
            'name',
            'short_description',
            'description',
            'sku',
            'status',
            'url',
            'url_key',
            'visibility',
        ];

        $stock = [
            'is_in_stock',
            'qty',
        ];

        $price = [
            'tax_class_id',
            'special_from_date',
            'special_to_date',
            'cost',
            'msrp',
        ];

        if (in_array($attributeCode, $primary)) {
            $group = __('Primary Attributes');
        } elseif (in_array($attributeCode, $stock)) {
            $group = __('Stock Attributes');
        } elseif (in_array($attributeCode, $price) || strpos($attributeCode, 'price') !== false) {
            $group = __('Prices & Taxes');
        } elseif (strpos($attributeCode, 'image') !== false || strpos($attributeCode, 'thumbnail') !== false) {
            $group = __('Images');
        } elseif (substr($attributeCode, 0, strlen('custom:')) == 'custom:') {
            $group = __('Custom Attributes');
        } elseif (substr($attributeCode, 0, strlen('mapping:')) == 'mapping:') {
            $group = __('Mapping');
        } elseif (strpos($attributeCode, 'category') !== false) {
            $group = __('Category');
        } elseif (strpos($attributeCode, 'ammeta') !== false) {
            $group = __('Amasty Meta Tags');
        } else {
            $group = __('Others Attributes');
        }

        return $group;
    }

    /************************/
}
