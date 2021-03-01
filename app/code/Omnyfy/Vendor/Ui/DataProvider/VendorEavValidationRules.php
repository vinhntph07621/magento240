<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-16
 * Time: 12:03
 */
namespace Omnyfy\Vendor\Ui\DataProvider;

use Omnyfy\Vendor\Api\Data\VendorAttributeInterface;

class VendorEavValidationRules
{
    /**
     * Build validation rules
     *
     * @param VendorAttributeInterface $attribute
     * @param array $data
     * @return array
     */
    public function build(VendorAttributeInterface $attribute, array $data)
    {
        $rules = [];
        if (!empty($data['required'])) {
            $rules['required-entry'] = true;
        }
        if ($attribute->getFrontendInput() === 'price') {
            $rules['validate-zero-or-greater'] = true;
        }

        $validationClasses = explode(' ', $attribute->getFrontendClass());

        foreach ($validationClasses as $class) {
            if (preg_match('/^maximum-length-(\d+)$/', $class, $matches)) {
                $rules = array_merge($rules, ['max_text_length' => $matches[1]]);
                continue;
            }
            if (preg_match('/^minimum-length-(\d+)$/', $class, $matches)) {
                $rules = array_merge($rules, ['min_text_length' => $matches[1]]);
                continue;
            }

            $rules = $this->mapRules($class, $rules);
        }

        return $rules;
    }

    /**
     * Map classes w. rules
     *
     * @param string $class
     * @param array $rules
     * @return array
     */
    protected function mapRules($class, array $rules)
    {
        switch ($class) {
            case 'validate-number':
            case 'validate-digits':
            case 'validate-email':
            case 'validate-url':
            case 'validate-alpha':
            case 'validate-alphanum':
                $rules = array_merge($rules, [$class => true]);
                break;
        }

        return $rules;
    }
}
 