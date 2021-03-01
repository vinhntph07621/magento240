<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-13
 * Time: 16:05
 */
namespace Omnyfy\Vendor\Model\Vendor\Attribute;

use Magento\Eav\Model\ResourceModel\Attribute\DefaultEntityAttributes\ProviderInterface;


class DefaultAttributes implements ProviderInterface
{
    public function getDefaultAttributes()
    {
        return ['entity_id', 'name', 'status', 'email', 'attribute_set_id', 'type_id',  'created_at', 'updated_at'];
    }
}
 