<?php

namespace Omnyfy\Core\Model\ResourceModel\Eav;

abstract class AbstractConfig extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Entity type id
     *
     * @var int
     */
    protected $_entityTypeId;

    /**
     * Store id
     *
     * @var int
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
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\Config $eavConfig,
        $connectionName = null
    ) {
        $this->_storeManager = $storeManager;
        $this->_eavConfig = $eavConfig;

        parent::__construct(
            $context,
            $connectionName
        );
    }

    /**
     * Retrieve entity type id
     *
     * @return int
     */
    abstract public function getEntityTypeId();

    /**
     * Initialize connection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('eav_attribute', 'attribute_id');
    }

    /**
     * Set store id
     *
     * @param integer $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = (int)$storeId;
        return $this;
    }

    /**
     * Return store id.
     * If is not set return current app store
     *
     * @return integer
     */
    public function getStoreId()
    {
        if ($this->_storeId === null) {
            $this->_storeId = (int)$this->_storeManager->getStore()->getId();
        }
        return $this->_storeId;
    }

    abstract public function getAttributesUsedInListing();
    /**
     * Retrieve attributes used in listing
     *
     * @return array
     */
    /*
    public function getAttributesUsedInListing()
    {
        $connection = $this->getConnection();
        $storeLabelExpr = $connection->getCheckSql('al.value IS NOT NULL', 'al.value', 'main_table.frontend_label');

        $select = $connection->select()->from(
            ['main_table' => $this->getTable('eav_attribute')]
        )->join(
            ['additional_table' => $this->getTable('omnyfy_vendor_eav_attribute')],
            'main_table.attribute_id = additional_table.attribute_id'
        )->joinLeft(
            ['al' => $this->getTable('eav_attribute_label')],
            'al.attribute_id = main_table.attribute_id AND al.store_id = ' . (int)$this->getStoreId(),
            ['store_label' => $storeLabelExpr]
        )->where(
            'main_table.entity_type_id = ?',
            $this->getEntityTypeId()
        )->where(
            'additional_table.used_in_listing = ?',
            1
        );
        return $connection->fetchAll($select);
    }
    */

}
