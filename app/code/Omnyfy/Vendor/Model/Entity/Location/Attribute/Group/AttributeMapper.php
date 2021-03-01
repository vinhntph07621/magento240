<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-24
 * Time: 17:03
 */
namespace Omnyfy\Vendor\Model\Entity\Location\Attribute\Group;

use Omnyfy\Vendor\Model\Location\Attribute;

class AttributeMapper implements AttributeMapperInterface
{
    /**
     * Unassignable attributes
     *
     * @var array
     */
    protected $unassignableAttributes;

    /**
     * @param \Omnyfy\Vendor\Model\Location\Attribute\Config $attributeConfig
     */
    public function __construct(\Omnyfy\Vendor\Model\Location\Attribute\Config $attributeConfig)
    {
        $this->unassignableAttributes = $attributeConfig->getAttributeNames('unassignable');
    }

    /**
     * Build attribute representation
     *
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @return array
     */
    public function map(\Magento\Eav\Model\Entity\Attribute $attribute)
    {
        $isUnassignable = !in_array($attribute->getAttributeCode(), $this->unassignableAttributes);

        return [
            'text' => $attribute->getAttributeCode(),
            'id' => $attribute->getAttributeId(),
            'cls' => $isUnassignable ? 'leaf' : 'system-leaf',
            'allowDrop' => false,
            'allowDrag' => true,
            'leaf' => true,
            'is_user_defined' => $attribute->getIsUserDefined(),
            'is_unassignable' => $isUnassignable,
            'entity_id' => $attribute->getEntityAttributeId()
        ];
    }
}
 