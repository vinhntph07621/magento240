<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-customer-segment
 * @version   1.0.51
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CustomerSegment\Model\Segment\Condition\Sales;


use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\AbstractModel;

class Purchasedqty extends AbstractSales
{
    /** define possible attribute options as constants */
    const TOTAL   = 'SUM';
    const AVERAGE = 'AVG';

    /**
     * Get HTML of condition string.
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
        . __('%1 Purchased Quantity %2 %3 while %4 of these Conditions match:',
            $this->getAttributeElementHtml(),
            $this->getOperatorElementHtml(),
            $this->getValueElementHtml(),
            $this->getAggregatorElement()->getHtml()
        )
        . $this->getRemoveLinkHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function asString($format = '')
    {
        return __('%1 Purchased Quantity %2 %3 while %4 of these Conditions match:',
            $this->getAttributeName(), $this->getOperatorName(), $this->getValueName(), $this->getAggregatorName()
        );
    }

    /**
     * Init select options for the attribute element.
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption([
            self::TOTAL   => __('Total'),
            self::AVERAGE => __('Average'),
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIfPart(AdapterInterface $adapter)
    {
        $operator = $this->operatorConverter->getSqlOperator($this->getData('operator'));
        $aggrFunc = $this->getData('attribute');
        $value = (int) $this->getValue();

        $ifPart = $adapter->getCheckSql(
            $aggrFunc . '(sales_order.total_qty_ordered) IS NOT NULL',
            $aggrFunc . '(sales_order.total_qty_ordered)',
            0
        );

        return $adapter->getCheckSql($ifPart . ' ' . $operator . ' ' . $value, 1, 0);
    }

    /**
     * {@inheritDoc}
     */
    /*public function getAttributeValue(AdapterInterface $adapter, AbstractModel $model)
    {
        $select = $adapter->select();

        $operator = $this->getSqlOperator($this->getOperator());
        $aggrFunc = $this->getAttribute();
        $value = (int) $this->getValue();

        $ifPart = $adapter->getCheckSql(
            $aggrFunc . '(sales_order.total_qty_ordered) IS NOT NULL',
            $aggrFunc . '(sales_order.total_qty_ordered)',
            0
        );

        $result = $adapter->getCheckSql($ifPart . ' ' . $operator . ' ' . $value, 1, 0);

        $select->from(
                ['sales_order' => $this->resourceConnection->getTableName('sales_order')],
                [
                    new \Zend_Db_Expr($result), // retrieve result of validation
                    //new \Zend_Db_Expr($aggrFunc . '(sales_order.total_qty_ordered)') // retrieve value
                ]
            )
            ->where('sales_order.store_id = ?', $model->getData('store_id'));

        if ($model->getData('customer_id')) {
            $select->where('sales_order.customer_id = ?', $model->getData('customer_id'));
        } else {
            $select->where('sales_order.customer_email = ?', $model->getData('email'));
        }

        if ($model->getData('matched_order_ids')) {
            $select->where('sales_order.entity_id IN(?)', $model->getData('matched_order_ids'));
        }

        return $adapter->fetchOne($select);
    }*/
}