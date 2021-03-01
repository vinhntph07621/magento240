<?php

namespace Omnyfy\LayeredNavigation\Model\ResourceModel\Layer\Filter;

class Attribute extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Initialize connection and define main table name
     *
     * @return void
     */
    protected function _construct()
    {
        // TODO: Create eav index table
        $this->_init('catalog_product_index_eav', 'entity_id');
    }

    /**
     * Apply attribute filter to product collection
     *
     * @param \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter
     * @param int $value
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return $this
     */
    public function applyFilterToCollection(\Omnyfy\LayeredNavigation\Model\Layer\Filter\AbstractFilter $filter, $value)
    {
        $collection = $filter->getLayer()->getCollection();
        $attribute = $filter->getAttributeModel();
        $connection = $this->getConnection();

        if ($collection instanceof \Omnyfy\Core\Model\ResourceModel\Flat\AbstractCollection && $collection->isEnabledFlat()) {

            if ($attribute->getFrontendInput() == 'multiselect'
                && $attribute->getIsFilterable() == \Omnyfy\LayeredNavigation\Model\Layer\Filter\AbstractFilter::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS
            ) {
                $conditions = [];
                $values = explode(',', $value);
                foreach ($values as $_value) {
                    $conditions[] = $connection->quoteInto(
                        "CONCAT(',', " . $filter->getData('collection_table_alias') . '.' . $attribute->getAttributeCode() . ", ',') LIKE ?",
                        '%,' . $_value . ',%'
                    );
                }
                $collection->getSelect()->where(implode(' OR ', $conditions));

            } else {
                $collection->addFilter(
                    $filter->getData('collection_table_alias') . '.' . $attribute->getAttributeCode(),
                    ['eq' => $value]
                );
            }

        } else {
            $tableAlias = $attribute->getAttributeCode() . '_idx';
            $conditions = [
                $tableAlias . '.entity_id = e.' . $collection->getIdFieldName(),
                $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
                $connection->quoteInto("{$tableAlias}.store_id = ?", $collection->getStoreId()),
                $connection->quoteInto("{$tableAlias}.value = ?", $value),
            ];

            $collection->getSelect()->join(
                [$tableAlias => $this->getMainTable()],
                implode(' AND ', $conditions),
                []
            );
        }

        return $this;
    }

    /**
     * Retrieve array with products counts per attribute option
     *
     * @param \Omnyfy\LayeredNavigation\Model\Layer\Filter\AbstractFilter $filter
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return array
     */
    public function getCount(\Omnyfy\LayeredNavigation\Model\Layer\Filter\AbstractFilter $filter)
    {
        $collection = $filter->getLayer()->getCollection();
        /* @var $collection \Magento\Eav\Model\Entity\Collection\AbstractCollection */

        // clone select from collection with filters
        $select = clone $collection->getSelect();

        // reset columns, order and limitation conditions
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);
        $select->reset(\Magento\Framework\DB\Select::ORDER);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);

        $connection = $this->getConnection();
        $attribute = $filter->getAttributeModel();

        if ($collection instanceof \Omnyfy\Core\Model\ResourceModel\Flat\AbstractCollection || $collection->isEnabledFlat()) {
            $select->columns([
                'value' => $filter->getData('collection_table_alias') . '.' . $attribute->getAttributeCode(),
                'count' => 'COUNT(e.' . $collection->getIdFieldName() . ')'
            ]);
            $select->group(
                $filter->getData('collection_table_alias') . '.' . $attribute->getAttributeCode()
            );

            if ($attribute->getFrontendInput() == 'multiselect'
                && $attribute->getIsFilterable() == \Omnyfy\LayeredNavigation\Model\Layer\Filter\AbstractFilter::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS
            ) {
                $values = $connection->fetchPairs($select);

                $_values = [];
                foreach($values as $value => $count) {
                    $options = explode(',', $value);

                    foreach($options as $option) {
                        if (isset($_values[$option])) {
                            $_values[$option] += $count;
                        } else {
                            $_values[$option] = $count;
                        }
                    }
                }

                return $_values;
            }

        } else {
            $tableAlias = sprintf('%s_idx', $attribute->getAttributeCode());
            $conditions = [
                $tableAlias . '.entity_id = e.' . $collection->getIdFieldName(),
                $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
                $connection->quoteInto("{$tableAlias}.store_id = ?", $filter->getStoreId()),
            ];

            $select->join(
                [$tableAlias => $this->getMainTable()],
                join(' AND ', $conditions),
                ['value', 'count' => new \Zend_Db_Expr("COUNT({$tableAlias}.{$collection->getIdFieldName()})")]
            )->group(
                "{$tableAlias}.value"
            );
        }

        return $connection->fetchPairs($select);
    }
}
