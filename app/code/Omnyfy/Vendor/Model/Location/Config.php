<?php

namespace Omnyfy\Vendor\Model\Location;

class Config extends \Omnyfy\Core\Model\Eav\AbstractConfig
{

    /**
     * Config factory
     *
     * @var \Omnyfy\Vendor\Model\Resource\Location\ConfigFactory
     */
    protected $_configFactory;

    /**
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Eav\Model\Entity\TypeFactory $entityTypeFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Type\CollectionFactory $entityTypeCollectionFactory
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Omnyfy\Vendor\Model\Resource\Location\ConfigFactory $configFactory
     */
    public function __construct(
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Eav\Model\Entity\TypeFactory $entityTypeFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Type\CollectionFactory $entityTypeCollectionFactory,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Omnyfy\Vendor\Model\Resource\Location\ConfigFactory $configFactory
    ) {
        $this->_configFactory = $configFactory;

        parent::__construct(
            $cache,
            $entityTypeFactory,
            $entityTypeCollectionFactory,
            $cacheState,
            $universalFactory,
            $storeManager,
            $eavConfig
        );
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\Vendor\Model\Resource\Location\Config');
    }

    /**
     * Retrieve resource model
     *
     * @return \Omnyfy\Vendor\Model\Resource\Location\Config
     */
    protected function _getResource()
    {
        return $this->_configFactory->create();
    }

    public function getAttributesUsedInListing()
    {
        if (is_null($this->_usedInListing)) {
            $this->_usedInListing = [];
            $entityType = \Omnyfy\Vendor\Model\Location::ENTITY;
            $attributesData = $this->_getResource()->setStoreId($this->getStoreId())->getAttributesUsedInListing();
            $this->_eavConfig->importAttributesData($entityType, $attributesData);
            foreach ($attributesData as $attributeData) {
                $attributeCode = $attributeData['attribute_code'];
                $this->_usedInListing[$attributeCode] = $this->_eavConfig->getAttribute(
                    $entityType,
                    $attributeCode
                );
            }
        }

        return $this->_usedInListing;
    }
}
