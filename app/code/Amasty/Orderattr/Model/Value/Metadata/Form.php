<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


namespace Amasty\Orderattr\Model\Value\Metadata;

use Amasty\Orderattr\Model\Config\Source\CheckoutStep;
use Magento\Framework\App\RequestInterface;

/**
 * @method \Amasty\Orderattr\Model\Attribute\Attribute[] getAllowedAttributes()
 * @method \Amasty\Orderattr\Model\Attribute\Attribute[] getAttributes()
 * @method \Amasty\Orderattr\Model\Attribute\Attribute|bool getAttribute($attributeCode)
 */
class Form extends \Magento\Eav\Model\Form
{
    /**
     * Current module pathname
     *
     * @var string
     */
    protected $_moduleName = 'Amasty_Orderattr';

    /**
     * Current EAV entity type code
     *
     * @var string
     */
    protected $_entityTypeCode = \Amasty\Orderattr\Model\ResourceModel\Entity\Entity::ENTITY_TYPE_CODE;

    /**
     * @var string
     */
    private $shippingMethod;

    /**
     * @var int
     */
    private $customerGroupId;

    /**
     * @var \Amasty\Orderattr\Model\ResourceModel\Attribute\CollectionFactory
     */
    private $attributeCollectionFactory;

    /**
     * @var \Amasty\Orderattr\Model\ResourceModel\Attribute\Relation\RelationDetails\CollectionFactory
     */
    private $attributeRelationCollectionFactory;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Module\Dir\Reader $modulesReader,
        \Magento\Eav\Model\AttributeDataFactory $attrDataFactory,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        RequestInterface $httpRequest,
        \Magento\Framework\Validator\ConfigFactory $validatorConfigFactory,
        \Amasty\Orderattr\Model\ResourceModel\Attribute\CollectionFactory $attributeCollectionFactory,
        \Amasty\Orderattr\Model\ResourceModel\Attribute\Relation\RelationDetails\CollectionFactory $attributeRelationCollectionFactory
    ) {
        parent::__construct(
            $storeManager,
            $eavConfig,
            $modulesReader,
            $attrDataFactory,
            $universalFactory,
            $httpRequest,
            $validatorConfigFactory
        );
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->attributeRelationCollectionFactory = $attributeRelationCollectionFactory;
    }

    /**
     * Return current entity instance
     *
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function getEntity()
    {
        if ($this->_entity === null) {
            $this->_entity = $this->_universalFactory->create(\Amasty\Orderattr\Model\Entity\EntityData::class);
        }

        return $this->_entity;
    }

    /**
     * Get EAV Entity Form Attribute Collection with applied filters
     *
     * @return \Amasty\Orderattr\Model\ResourceModel\Attribute\Collection
     */
    protected function _getFilteredFormAttributeCollection()
    {
        $this->_allowedAttributes = $this->_systemAttributes = [];

        $collection = $this->_getFormAttributeCollection()
            ->addAttributeGrouping()
            ->setSortOrder();

        if ($this->_ignoreInvisible) {
            if ($this->_store) {
                $collection->addStoreFilter($this->_store->getId());
            }

            if ($this->getCustomerGroupId() !== null) {
                $collection->addCustomerGroupFilter($this->getCustomerGroupId());
            }

            if ($this->getShippingMethod() !== null) {
                $collection->addShippingMethodsFilter($this->getShippingMethod());
            }
        }

        return $collection;
    }

    /**
     * Get EAV Entity Form Attribute Collection
     *
     * @return \Amasty\Orderattr\Model\ResourceModel\Attribute\Collection
     */
    protected function _getFormAttributeCollection()
    {
        return $this->attributeCollectionFactory->create();
    }

    /**
     * @return int
     */
    public function getCustomerGroupId()
    {
        return $this->customerGroupId;
    }

    /**
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->shippingMethod;
    }

    /**
     * Whether the specified attribute needs to skip rendering/validation
     *
     * @param \Amasty\Orderattr\Model\Attribute\Attribute $attribute
     *
     * @return bool
     */
    protected function _isAttributeOmitted($attribute)
    {
        if ($this->_ignoreInvisible
            && (!$this->isAttributeVisibleForCurrentFormCode($attribute)
                || ($this->getShippingMethod()
                    && !empty($attribute->getShippingMethods())
                    && !in_array($this->getShippingMethod(), $attribute->getShippingMethods())
                )
            )
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param string $shippingMethod
     *
     * @return $this
     */
    public function setShippingMethod($shippingMethod)
    {
        $this->shippingMethod = $shippingMethod;

        return $this;
    }

    /**
     * @param int $customerGroupId
     *
     * @return $this
     */
    public function setCustomerGroupId($customerGroupId)
    {
        $this->customerGroupId = (int)$customerGroupId;

        return $this;
    }

    /**
     * Whether the specified attribute needs to skip rendering/validation
     *
     * @param \Amasty\Orderattr\Model\Attribute\Attribute $attribute
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool
     */
    protected function isAttributeVisibleForCurrentFormCode($attribute)
    {
        switch ($this->getFormCode()) {
            case 'adminhtml_checkout':
            case 'adminhtml_order_view':
                return (bool)$attribute->getIsVisibleOnBack();
            case 'amasty_checkout_shipping':
                return (bool)$attribute->getIsVisibleOnFront()
                    && in_array(
                        $attribute->getCheckoutStep(),
                        [
                            CheckoutStep::SHIPPING_STEP,
                            CheckoutStep::SHIPPING_METHODS
                        ]
                    );
            case 'amasty_checkout_virtual':
                return (bool)$attribute->getIsVisibleOnFront()
                    && in_array(
                        $attribute->getCheckoutStep(),
                        [
                            CheckoutStep::PAYMENT_STEP,
                            CheckoutStep::PAYMENT_PLACE_ORDER,
                            CheckoutStep::ORDER_SUMMARY
                        ]
                    );
            case 'frontend_order_print':
                return (bool)$attribute->getIsVisibleOnFront()
                    && $attribute->getIncludeInHtmlPrintOrder();
            case 'frontend_order_email':
                return (bool)$attribute->getIsVisibleOnFront()
                    && $attribute->isIncludeInEmail();
            case 'adminhtml_order_inline_edit':
                return (bool)$attribute->getIsVisibleOnBack() &&
                    (bool)$attribute->isShowOnGrid();
            case 'adminhtml_order_print':
                return (bool)$attribute->getIsVisibleOnBack()
                    && $attribute->getIncludeInPdf();
        }

        return (bool)$attribute->getIsVisibleOnFront();
    }

    protected function isAdminArea()
    {
        switch ($this->getFormCode()) {
            case 'adminhtml_checkout':
            case 'adminhtml_order_view':
            case 'adminhtml_order_print':
            case 'adminhtml_order_inline_edit':
                return true;
        }
        return false;
    }

    /**
     * Restore data array to current entity
     *
     * @param array $data
     * @return $this
     */
    public function restoreData(array $data)
    {
        if ($this->_ignoreInvisible && !$this->isAdminArea()) {
            $this->modifyAvailableAttributesByData($data);
        }
        return parent::restoreData($data);
    }

    /**
     * Remove order attribute value if attribute hided by relation
     *
     * @param array $data
     */
    public function modifyAvailableAttributesByData($data)
    {
        if (empty($data)) {
            return;
        }

        foreach ($this->getAllowedAttributes() as $attribute) {
            if ($attribute->getFrontendInput() === 'html') {
                unset($this->_allowedAttributes[$attribute->getAttributeCode()]);
            }
        }

        /** @var \Amasty\Orderattr\Model\ResourceModel\Attribute\Relation\RelationDetails\Collection $collection */
        $collection = $this->attributeRelationCollectionFactory->create();
        $collection->joinDependAttributeCode();

        $attributesToSave = [];
        /** @var \Amasty\Orderattr\Model\Attribute\Relation\RelationDetails $relation */
        foreach ($collection->getItems() as $relation) {
            if (!array_key_exists($relation->getData('parent_attribute_code'), $data)
                || (
                    isset($attributesToSave[$relation->getData('parent_attribute_code')])
                    && !$attributesToSave[$relation->getData('parent_attribute_code')]
                )
            ) {
                $attributesToSave[$relation->getData('dependent_attribute_code')] = false;
                $attributesToSave = $this->validateNestedRelations($attributesToSave, $collection);
                //unset nested
            } else {
                foreach ($data as $attributeCode => $attributeValue) {
                    // is attribute have relations
                    if ($relation->getData('parent_attribute_code') == $attributeCode) {
                        $code = $relation->getData('dependent_attribute_code');
                        if (is_array($attributeValue) && count($attributeValue) === 1) {
                            $attributeValue = explode(',', current($attributeValue));
                        }
                        /**
                         * Is not to show - hide;
                         * false - value should to be saved and validated
                         */
                        $attributesToSave[$code] = (bool)
                            (isset($attributesToSave[$code]) && $attributesToSave[$code])
                            || $relation->getOptionId() == $attributeValue
                            || (is_array($attributeValue) && in_array($relation->getOptionId(), $attributeValue));
                    }
                }
            }
        }
        $attributesToSave = $this->validateNestedRelations($attributesToSave, $collection);
        foreach (array_keys($this->getAllowedAttributes()) as $attributeCode) {
            if (array_key_exists($attributeCode, $attributesToSave) && !$attributesToSave[$attributeCode]) {
                unset($this->_allowedAttributes[$attributeCode]);
            }
        }
    }

    /**
     * Check relation chain.
     * Example: we have
     *      relation1 - attribute1 = someAttribute1, dependAttribute1 = hidedSelect1
     *      relation2 - attribute2 = hidedSelect1, dependAttribute2 = someAttribute2
     *  where relation1.dependAttribute1 == relation2.attribute2
     *
     * @param array                                                                               $isValidArray
     * @param \Amasty\Orderattr\Model\ResourceModel\Attribute\Relation\RelationDetails\Collection $relations
     *
     * @return array
     */
    private function validateNestedRelations($isValidArray, $relations)
    {
        $isNestedFind = false;
        foreach ($relations->getItems() as $relation) {
            $parentCode = $relation->getData('parent_attribute_code');
            $dependCode = $relation->getData('dependent_attribute_code');
            if (array_key_exists($parentCode, $isValidArray) && !$isValidArray[$parentCode]
                && (!array_key_exists($dependCode, $isValidArray) || $isValidArray[$dependCode])
            ) {
                $isValidArray[$dependCode] = false;
                $isNestedFind = true;
            }
        }
        if ($isNestedFind) {
            $isValidArray = $this->validateNestedRelations($isValidArray, $relations);
        }

        return $isValidArray;
    }

    /**
     * Set whether invisible attributes should be ignored.
     *
     * @param bool $ignoreInvisible
     *
     * @return $this
     */
    public function setInvisibleIgnored($ignoreInvisible)
    {
        $this->_ignoreInvisible = $ignoreInvisible;

        return $this;
    }
}
