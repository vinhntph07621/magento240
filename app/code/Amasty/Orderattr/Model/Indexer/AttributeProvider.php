<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


namespace Amasty\Orderattr\Model\Indexer;

use Magento\Framework\Indexer\FieldsetInterface;
use Magento\Eav\Model\Config;
use Amasty\Orderattr\Model\Attribute\Attribute;

class AttributeProvider implements FieldsetInterface
{
    /**
     * EAV entity
     */
    const ENTITY = \Amasty\Orderattr\Model\ResourceModel\Entity\Entity::ENTITY_TYPE_CODE;

    /**
     * @var Attribute[]
     */
    protected $attributes;

    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @param Config $eavConfig
     */
    public function __construct(
        Config $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
    }

    /**
     * Add EAV attribute fields to fieldset
     *
     * @param array $data
     * @return array
     */
    public function addDynamicData(array $data)
    {
        $additionalFields = $this->convert($this->getAttributes(), $data);
        $data['fields'] = $this->merge($data['fields'], $additionalFields);

        return $data;
    }

    /**
     * Retrieve all attributes
     *
     * @return Attribute[]
     */
    private function getAttributes()
    {
        if ($this->attributes === null) {
            $this->attributes = [];
            $entityType = $this->eavConfig->getEntityType(static::ENTITY);
            /** @var Attribute[] $attributes */
            $attributes = $entityType->getAttributeCollection()->getItems();
            /** @var \Amasty\Orderattr\Model\ResourceModel\Entity\Entity $entity */
            $entity = $entityType->getEntity();

            foreach ($attributes as $attribute) {
                $attribute->setEntity($entity);
            }
            $this->attributes = $attributes;
        }

        return $this->attributes;
    }

    /**
     * Convert attributes to fields
     *
     * @param Attribute[] $attributes
     * @param array $fieldset
     * @return array
     */
    protected function convert(array $attributes, array $fieldset)
    {
        $fields = [];
        foreach ($attributes as $attribute) {
            if (!$attribute->isStatic()) {
                if ($attribute->isShowOnGrid()) {
                    $fields[$attribute->getName()] = [
                        'name' => $attribute->getName(),
                        'handler' => 'Magento\Framework\Indexer\Handler\AttributeHandler',
                        'origin' => $attribute->getName(),
                        'type' => $this->getType($attribute),
                        'dataType' => $attribute->getBackendType(),
                        'filters' => [],
                        'entity' => static::ENTITY,
                        'bind' => isset($fieldset['references']['entity']['to'])
                            ? $fieldset['references']['entity']['to']
                            : null,
                    ];
                }
            } else {
                $fields[$attribute->getName()] = [
                    'type' => $this->getType($attribute),
                ];
            }
        }

        return $fields;
    }

    /**
     * Get field type for attribute
     *
     * @param Attribute $attribute
     * @return string
     */
    protected function getType(Attribute $attribute)
    {
        if ($attribute->canBeFilterableInGrid()) {
            $type = 'filterable';
        } else {
            $type = 'virtual';
        }

        return $type;
    }

    /**
     * Merge fields with attribute fields
     *
     * @param array $dataFields
     * @param array $searchableFields
     * @return array
     */
    protected function merge(array $dataFields, array $searchableFields)
    {
        foreach ($searchableFields as $name => $field) {
            if (!isset($field['name']) && !isset($dataFields[$name])) {
                continue;
            }
            if (!isset($dataFields[$name])) {
                $dataFields[$name] = [];
            }
            foreach ($field as $key => $value) {
                $dataFields[$name][$key] = $value;
            }
        }

        return $dataFields;
    }
}
