<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_ProductAttributesImportExport
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ProductAttributesImportExport\Model\Import\ProductAttributes\Validator;

use Magento\Framework\Validator\AbstractValidator;
use Bss\ProductAttributesImportExport\Model\Import\ProductAttributes\RowValidatorInterface;

/**
 * Class Attribute
 * @package Bss\ProductAttributesImportExport\Model\Import\ProductAttributes\Validator
 */
class Attribute extends AbstractValidator implements RowValidatorInterface
{
    protected $inputType;
    /**
     * @var \Magento\CatalogImportExport\Model\Import\Product $context
     */
    protected $context;

    /**
     * Attribute constructor.
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Inputtype $inputtype
     */
    public function __construct(\Magento\Catalog\Model\Product\Attribute\Source\Inputtype $inputtype)
    {
        $this->inputType = $inputtype;
    }

    /**
     * Initialize validator
     *
     * @param \Magento\CatalogImportExport\Model\Import\Product $context
     * @return $this
     */
    public function init($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @param mixed $value
     * @return bool|void
     * @throws \Zend_Validate_Exception
     */
    public function isValid($value)
    {
        parent::isValid($value);
    }

    /**
     * Validate column is_global
     * @param array $rowData
     * @return bool
     */
    public function validateIsGlobal($rowData)
    {
        $value[0] = '0';
        $value[1] = '1';
        $value[2] = '2';
        if (!in_array($rowData['is_global'], $value)) {
            return false;
        }
        return true;
    }

    /**
     * Validate column swatch_input_type
     * @param array $rowData
     * @return bool
     */
    public function validateSwatchInputType($rowData)
    {
        $value[0] = 'text';
        $value[1] = 'visual';
        if (!in_array($rowData['swatch_input_type'], $value) && $rowData['swatch_input_type']!="") {
            return false;
        }
        return true;
    }

    /**
     * Validate column frontend_input
     * @param array $rowData
     * @return bool
     */
    public function validateFrontendInput($rowData)
    {
        $validFrontendInput = [];
        foreach ($this->inputType->toOptionArray() as $item) {
            $validFrontendInput[] = $item['value'];
        }
        $additionalFrontendInput = [
            'image',
            'gallery',
            'hidden',
            'multiline',
            'weight'
        ];
        $validFrontendInput = array_merge($validFrontendInput, $additionalFrontendInput);
        if (!in_array($rowData['frontend_input'], $validFrontendInput) &&
            $rowData['frontend_input']!=""
        ) {
            return false;
        }
        return true;
    }

    /**
     * Validate column backend_type
     * @param array $rowData
     * @return bool
     */
    public function validateBackendType($rowData)
    {
        $validBackendType = [
            'static',
            'varchar',
            'int',
            'text',
            'datetime',
            'decimal'
        ];

        if ($rowData['backend_type'] == "") {
            return false;
        }

        if (!in_array($rowData['backend_type'], $validBackendType)) {
            return false;
        }

        return true;
    }

    /**
     * Validate column source_model
     * @param array $rowData
     * @return bool
     */
    public function validateSourceModel($rowData)
    {
        if ($rowData['source_model']!="") {
            return class_exists($rowData['source_model']);
        }
        return true;
    }

    /**
     * Validate column backend_model
     * @param array $rowData
     * @return bool
     */
    public function validateBackendModel($rowData)
    {
        if ($rowData['backend_model']!="") {
            return class_exists($rowData['backend_model']);
        }
        return true;
    }

    /**
     * Validate product type in column apply_to
     * @param array $rowData
     * @param string $separator
     * @return null|string
     */
    public function validateProductType($rowData, $separator)
    {
        $applyTo = "";
        $validProductType = [
            'simple',
            'virtual',
            'downloadable',
            'bundle',
            'configurable',
            'grouped'
        ];

        $inputProductType = explode($separator, $rowData['apply_to']);
        foreach ($inputProductType as $productType) {
            if (in_array($productType, $validProductType)) {
                $applyTo .= $productType . ",";
            }
        }
        $applyTo = rtrim($applyTo, ",");
        if (empty($applyTo)) {
            return null;
        }
        return $applyTo;
    }

    /**
     * Validate correct columns
     * @param array $rowData
     * @param array $headers
     * @return bool
     */
    public function isMissingColumn($rowData, $headers)
    {
        foreach ($headers as $header) {
            if (!isset($rowData[$header])) {
                return $header;
            }
        }
        return false;
    }

    /**
     * @param array $attributesOptions
     * @param string $multipleSeparator
     * @param string $optionValueSeparator
     * @return bool
     */
    public function checkAttributeOptions($attributesOptions, $multipleSeparator, $optionValueSeparator)
    {
        if ($attributesOptions != "") {
            if (strpos($attributesOptions, $multipleSeparator) === false) {
                return false;
            }
            foreach (explode($multipleSeparator, $attributesOptions) as $option) {
                if ($option != '') {
                    $checkString = 'admin' . $optionValueSeparator;
                    if (strpos($option, $checkString) === false) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * @param array $attributeOptionsSwatch
     * @param string $multipleSeparator
     * @param string $optionValueSeparator
     * @return bool
     */
    public function checkAttributeOptionsSwatch($attributeOptionsSwatch, $multipleSeparator, $optionValueSeparator)
    {
        if ($attributeOptionsSwatch != "") {
            if (strpos($attributeOptionsSwatch, $multipleSeparator) === false) {
                return false;
            }
            foreach (explode($multipleSeparator, $attributeOptionsSwatch) as $option) {
                if ($option != '') {
                    $checkString = 'admin' . $optionValueSeparator;

                    if (strpos($option, $checkString) === false) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * @param string $swatchImage
     * @param string $swatchLength
     * @return int
     */
    protected function setSwatchLength($swatchImage, $swatchLength)
    {
        if (strpos($swatchImage, '.jpg') !== false ||
            strpos($swatchImage, '.jpeg') !== false ||
            strpos($swatchImage, '.png') !== false ||
            strpos($swatchImage, '.gif') !== false ||
            strpos($swatchImage, '.tiff') !== false
        ) {
            $swatchLength = 50;
        }
        return $swatchLength;
    }
}
