<?php
/**
 * Project: Updated note.
 * Author: seth
 * Date: 25/11/19
 * Time: 12:26 pm
 **/

namespace Omnyfy\BackendTheme\Ui\DataProvider\Product\Form\Modifier;


use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;

class CustomOptions extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions
{
    /**
     * Get config for "Maximum Image Width" field
     *
     * @param int $sortOrder
     * @return array
     */
    protected function getImageSizeXFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Maximum Image Size'),
                        'notice' => __('Please note: Adding a Maximum Image Size will restrict the file upload field to allow only image file types, such as png and jpg. Keep this blank if you would like to have all files available in the extensions list.'),
                        'addbefore' => "",
                        'addafter' => __('px.'),
                        'component' => 'Magento_Catalog/js/components/custom-options-component',
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'dataScope' => CustomOptions::FIELD_IMAGE_SIZE_X_NAME,
                        'dataType' => Number::NAME,
                        'sortOrder' => $sortOrder,
                        'validation' => [
                            'validate-zero-or-greater' => true
                        ],
                    ],
                ],
            ],
        ];
    }
}