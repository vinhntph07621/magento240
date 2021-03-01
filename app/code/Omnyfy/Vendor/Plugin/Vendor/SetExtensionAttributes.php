<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-06-20
 * Time: 11:11
 */
namespace Omnyfy\Vendor\Plugin\Vendor;

class SetExtensionAttributes
{
    protected $fields = [
        'distance'
    ];

    public function aroundGetExtensionAttributes($subject, callable $proceed) {
        $result = $proceed();

        foreach($this->fields as $field) {
            if ($subject->hasData($field)) {
                $result->setData($field, $subject->getData($field));
            }
        }
        return $result;
    }
}
 