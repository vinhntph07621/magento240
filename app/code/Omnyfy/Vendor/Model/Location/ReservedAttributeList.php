<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-01
 * Time: 17:55
 */
namespace Omnyfy\Vendor\Model\Location;


class ReservedAttributeList extends \Magento\Catalog\Model\Product\ReservedAttributeList
{
    /**
     * @var string[]
     */
    protected $_reservedAttributes;

    /**
     * @param string $vendorModel
     * @param array $reservedAttributes
     * @param array $allowedAttributes
     */
    public function __construct($vendorModel, array $reservedAttributes = [], array $allowedAttributes = [])
    {
        $methods = get_class_methods($vendorModel);
        foreach ($methods as $method) {
            if (preg_match('/^get([A-Z]{1}.+)/', $method, $matches)) {
                $method = $matches[1];
                $tmp = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $method));
                $reservedAttributes[] = $tmp;
            }
        }
        $this->_reservedAttributes = array_diff($reservedAttributes, $allowedAttributes);
    }

    /**
     * Check whether attribute reserved or not
     *
     * @param \Omnyfy\Vendor\Model\Entity\Vendor\Attribute $attribute
     * @return boolean
     */
    public function isReservedAttribute($attribute)
    {
        return $attribute->getIsUserDefined() && in_array($attribute->getAttributeCode(), $this->_reservedAttributes);
    }
}
 