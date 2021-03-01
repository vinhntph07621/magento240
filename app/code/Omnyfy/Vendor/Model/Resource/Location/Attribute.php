<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-01
 * Time: 16:38
 */
namespace Omnyfy\Vendor\Model\Resource\Location;

use Magento\Catalog\Model\Attribute\LockValidatorInterface;
use Omnyfy\Vendor\Api\Data\LocationInterface;

class Attribute extends \Magento\Eav\Model\ResourceModel\Entity\Attribute
{
    protected $_eavConfig;

    protected $attrLockValidator;

    protected $metadataPool;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\ResourceModel\Entity\Type $eavEntityType,
        \Magento\Eav\Model\Config $eavConfig,
        LockValidatorInterface $lockValidator,
        $connectionName = null)
    {
        $this->attrLockValidator = $lockValidator;
        $this->_eavConfig = $eavConfig;
        parent::__construct($context, $storeManager, $eavEntityType, $connectionName);
    }

    /**
     * Perform actions after object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->_clearUselessAttributeValues($object);
        return parent::_afterSave($object);
    }

    /**
     * Clear useless attribute values
     *
     * @param  \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _clearUselessAttributeValues(\Magento\Framework\Model\AbstractModel $object)
    {
        $origData = $object->getOrigData();

        if ($object->isScopeGlobal() && isset(
                $origData['is_global']
            ) && \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL != $origData['is_global']
        ) {
            $attributeStoreIds = array_keys($this->_storeManager->getStores());
            if (!empty($attributeStoreIds)) {
                $delCondition = [
                    'attribute_id = ?' => $object->getId(),
                    'store_id IN(?)' => $attributeStoreIds,
                ];
                $this->getConnection()->delete($object->getBackendTable(), $delCondition);
            }
        }

        return $this;
    }

    /**
     * Delete entity
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteEntity(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getEntityAttributeId()) {
            return $this;
        }

        $result = $this->getEntityAttribute($object->getEntityAttributeId());
        if ($result) {
            $attribute = $this->_eavConfig->getAttribute(
                $object->getEntityTypeId(),
                $result['attribute_id']
            );

            try {
                $this->attrLockValidator->validate($attribute, $result['attribute_set_id']);
            } catch (\Magento\Framework\Exception\LocalizedException $exception) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Attribute \'%1\' is locked. %2', $attribute->getAttributeCode(), $exception->getMessage())
                );
            }

            $backendTable = $attribute->getBackend()->getTable();
            if ($backendTable) {
                $linkField = $this->getMetadataPool()
                    ->getMetadata(LocationInterface::class)
                    ->getLinkField();

                $select = $this->getConnection()->select()->from(
                    $attribute->getEntity()->getEntityTable(),
                    $linkField
                )->where(
                    'attribute_set_id = ?',
                    $result['attribute_set_id']
                );

                $clearCondition = [
                    'attribute_id =?' => $attribute->getId(),
                    $linkField . ' IN (?)' => $select,
                ];
                $this->getConnection()->delete($backendTable, $clearCondition);
            }
        }

        $condition = ['entity_attribute_id = ?' => $object->getEntityAttributeId()];
        $this->getConnection()->delete($this->getTable('eav_entity_attribute'), $condition);

        return $this;
    }

    /**
     * @return \Magento\Framework\EntityManager\MetadataPool
     */
    private function getMetadataPool()
    {
        if (null === $this->metadataPool) {
            $this->metadataPool = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Framework\EntityManager\MetadataPool');
        }
        return $this->metadataPool;
    }
}

 