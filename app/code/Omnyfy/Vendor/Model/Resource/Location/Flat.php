<?php

namespace Omnyfy\Vendor\Model\Resource\Location;

class Flat extends \Omnyfy\Core\Model\ResourceModel\Flat\AbstractResource
{

    const ENTITY_TYPE = \Omnyfy\Vendor\Model\Location::ENTITY;
    const FLAT_TABLE_NAME = 'omnyfy_vendor_location_flat';

    /**
     * @var \\Omnyfy\Vendor\Model\Location\Attribute\DefaultAttributes
     */
    protected $_defaultAttributes;

    /**
     * @var \Omnyfy\Vendor\Model\Location\Config
     */
    protected $_eavConfig;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Omnyfy\Vendor\Model\Location\Config $eavConfig
     * @param \Omnyfy\Vendor\Model\Location\Attribute\DefaultAttributes $defaultAttributes
     * @param type $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Omnyfy\Vendor\Model\Location\Config $eavConfig, // Overridden
        \Omnyfy\Vendor\Model\Location\Attribute\DefaultAttributes $defaultAttributes,
        string $connectionName = null
    ) {
        $this->_defaultAttributes = $defaultAttributes;

        parent::__construct(
            $context,
            $storeManager,
            $eavConfig,
            $connectionName
        );
    }

    /**
     * Retrieve default entity static attributes
     *
     * @return string[]
     */
    public function getDefaultAttributes()
    {
        return array_unique(
            array_merge(
                $this->_defaultAttributes->getDefaultAttributes(),
                [$this->getEntityIdField()]
            )
        );
    }

}
