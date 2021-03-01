<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-23
 * Time: 11:34
 */
namespace Omnyfy\Vendor\Helper;

class Vendor
{
    public function getAttributeInputTypes($inputType = null)
    {
        /**
         * @todo specify there all relations for properties depending on input type
         */
        $inputTypes = [
            'multiselect' => ['backend_model' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend'],
            'boolean' => ['source_model' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean'],
            'image' => ['backend_model' => 'Omnyfy\Vendor\Model\Vendor\Attribute\Backend\Media'],
        ];

        if ($inputType === null) {
            return $inputTypes;
        } else {
            if (isset($inputTypes[$inputType])) {
                return $inputTypes[$inputType];
            }
        }
        return [];
    }

    public function getAttributeSourceModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['source_model'])) {
            return $inputTypes[$inputType]['source_model'];
        }
        return null;
    }

    public function getAttributeBackendModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['backend_model'])) {
            return $inputTypes[$inputType]['backend_model'];
        }
        return null;
    }
}
 