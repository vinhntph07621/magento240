<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-16
 * Time: 16:37
 */
namespace Omnyfy\Vendor\Model\Entity\Vendor\Attribute\Group;

use Magento\Eav\Model\Entity\Attribute;

interface AttributeMapperInterface
{
    /**
     * Map Attribute to presentation format
     *
     * @param Attribute $attribute
     * @return array
     */
    public function map(Attribute $attribute);
}
 