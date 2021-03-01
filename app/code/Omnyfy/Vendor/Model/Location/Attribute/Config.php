<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-24
 * Time: 17:05
 */
namespace Omnyfy\Vendor\Model\Location\Attribute;

class Config
{
    /**
     * @var \Omnyfy\Vendor\Model\Location\Attribute\Config\Data
     */
    protected $_dataStorage;

    /**
     * @param \Omnyfy\Vendor\Model\Location\Attribute\Config\Data $dataStorage
     */
    public function __construct(\Omnyfy\Vendor\Model\Location\Attribute\Config\Data $dataStorage)
    {
        $this->_dataStorage = $dataStorage;
    }

    /**
     * Retrieve names of attributes belonging to specified group
     *
     * @param string $groupName Name of an attribute group
     * @return array
     */
    public function getAttributeNames($groupName)
    {
        return $this->_dataStorage->get($groupName, []);
    }
}
 