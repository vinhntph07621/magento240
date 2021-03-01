<?php

namespace Omnyfy\Vendor\Model\Location\Attribute;

use Magento\Eav\Model\ResourceModel\Attribute\DefaultEntityAttributes\ProviderInterface;

class DefaultAttributes implements ProviderInterface
{

    /**
     * Retrieve default entity static attributes
     *
     * @return string[]
     */
    public function getDefaultAttributes()
    {
        return ['entity_id', 'vendor_id', 'vendor_type_id', 'attribute_set_id', 'is_warehouse', 'status', 'priority'];
    }

}
