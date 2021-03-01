<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 8/9/17
 * Time: 10:54 AM
 */
namespace Omnyfy\Vendor\Model\Resource\Setup;

use Magento\Eav\Model\Entity\Setup\PropertyMapperAbstract;

class PropertyMapper extends PropertyMapperAbstract
{
    public function map(array $input, $entityTypeId)
    {
        return [
            'is_visible' => $this->_getValue($input, 'visible', 1),
            'is_searchable' => $this->_getValue($input, 'searchable', 0),
            'is_filterable' => $this->_getValue($input, 'filterable', 0),
            'used_in_listing' => $this->_getValue($input, 'used_in_listing', 0),
            'is_used_in_grid' => $this->_getValue($input, 'is_used_in_grid', 0),
            'is_visible_in_grid' => $this->_getValue($input, 'is_visible_in_grid', 0),
            'is_filterable_in_grid' => $this->_getValue($input, 'is_filterable_in_grid', 0),
            'position' => $this->_getValue($input, 'position', 0),
        ];
    }
}