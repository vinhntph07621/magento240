<?php

namespace Omnyfy\Core\Model\Eav;

abstract class AbstractConfig extends \Magento\Eav\Model\Config
{

    /**
     * @var mixed
     */
    protected $_attributeSetsById;

    /**
     * @var mixed
     */
    protected $_attributeSetsByName;

    /**
     * @var mixed
     */
    protected $_attributeGroupsById;

    /**
     * @var mixed
     */
    protected $_attributeGroupsByName;

    /**
     * Array of attributes codes needed for load
     *
     * @var array
     */
    protected $_attributes;

    /**
     * Attributes used in listing
     *
     * @var array
     */
    protected $_usedInListing;

    /**
     * @var int|float|string|null
     */
    protected $_storeId = null;

    /**
     * Eav config
     *
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Eav\Model\Entity\TypeFactory $entityTypeFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Type\CollectionFactory $entityTypeCollectionFactory
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Eav\Model\Entity\TypeFactory $entityTypeFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Type\CollectionFactory $entityTypeCollectionFactory,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->_storeManager = $storeManager;
        $this->_eavConfig = $eavConfig;

        parent::__construct(
            $cache,
            $entityTypeFactory,
            $entityTypeCollectionFactory,
            $cacheState,
            $universalFactory
        );
    }

    /**
     * Retrieve resource model
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    abstract protected function _getResource();

    /**
     * Return store id, if is not set return current app store
     *
     * @return integer
     */
    public function getStoreId()
    {
        if ($this->_storeId === null) {
            return $this->_storeManager->getStore()->getId();
        }

        return $this->_storeId;
    }

    /**
     * Set store id
     *
     * @param integer $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;

        return $this;
    }

    /**
     * @param \Magento\Framework\DataObject $source
     * @param string $value
     * @return null|mixed
     */
    public function getSourceOptionId($source, $value)
    {
        foreach ($source->getAllOptions() as $option) {
            if (strcasecmp($option['label'], $value) == 0 || $option['value'] == $value) {
                return $option['value'];
            }
        }

        return null;
    }

    /**
     * Load attributes
     *
     * @return array
     */
    public function getAttributes($entityType = null)
    {
        if (is_null($this->_attributes)) {
            $this->_attributes = array_keys($this->getAttributesUsedInListing());
        }

        return $this->_attributes;
    }

    abstract public function getAttributesUsedInListing();

    /**
     * Retrieve Attributes used in provider listing
     *
     * @return array
     */
    /*
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
    */
}
