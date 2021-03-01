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

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\Combine;
use Magento\Rule\Model\Condition\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Mirasvit\CustomerSegment\Api\Service\OperatorConversionInterface;
use Mirasvit\CustomerSegment\Model\Segment\Condition\Daterange;

/**
 * @method \Magento\Rule\Model\Condition\AbstractCondition[] getConditionOptions() - Added through the di.xml
 */
abstract class AbstractSales extends Combine
{
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * {@inheritdoc}
     */
    protected $_inputType = 'numeric';
    /**
     * @var OperatorConversionInterface
     */
    protected $operatorConverter;
    /**
     * @var Daterange
     */
    protected $dateRange;

    /**
     * Salesamount constructor.
     *
     * For \Magento\Rule\Model\Condition\Combine type we have to declare the type and value params
     * after calling the parent construct because it rewrites settings declared via di.xml.
     *
     * @param OperatorConversionInterface $operatorConverter
     * @param SearchCriteriaBuilder       $searchCriteriaBuilder
     * @param OrderRepositoryInterface    $orderRepository
     * @param ResourceConnection          $resourceConnection
     * @param Context                     $context
     * @param array                       $data
     */
    public function __construct(
        OperatorConversionInterface $operatorConverter,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderRepositoryInterface $orderRepository,
        ResourceConnection $resourceConnection,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setType(get_class($this));
        $this->setValue(null);
        $this->resourceConnection = $resourceConnection;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->operatorConverter = $operatorConverter;
    }

    /**
     * Get If Part for use in select to validate condition
     *
     * @param AdapterInterface $adapter
     *
     * @return \Zend_Db_Expr
     */
    abstract public function getIfPart(AdapterInterface $adapter);

    /**
     * {@inheritdoc}
     */
    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();
        foreach ($this->getConditionOptions() as $condition) {
            $conditions[] = [
                'value' => $condition->getData('type'),
                'label' => $condition->getData('label'),
            ];
        }

        return $conditions;
    }

    /**
     * Get input type for value element.
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Reset Value Option defined in parent class to prevent displaying incorrect values.
     *
     * @return $this
     */
    public function loadValueOptions()
    {
        $this->setValueOption([]);

        return $this;
    }

    /**
     * Customize default operator input by type mapper for some types.
     *
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            parent::getDefaultOperatorInputByType();
            $this->_defaultOperatorInputByType['numeric'] = array('==', '!=', '>=', '>', '<=', '<');
        }
        return $this->_defaultOperatorInputByType;
    }

    /**
     * Add operator when loading array
     *
     * @param array $arr
     * @param string $key
     *
     * @return $this
     */
    public function loadArray($arr, $key = 'conditions')
    {
        if (isset($arr['operator'])) {
            $this->setOperator($arr['operator']);
        }

        if (isset($arr['attribute'])) {
            $this->setAttribute($arr['attribute']);
        }

        return parent::loadArray($arr, $key);
    }

    /**
     * @todo add param depending on which method will return - 'Select Value' or 'Result of Validation'
     *
     * @param AdapterInterface $adapter
     * @param AbstractModel $model
     *
     * @return string
     */
    public function getAttributeValue(AdapterInterface $adapter, AbstractModel $model)
    {
        $select = $adapter->select();
        $ifPart = $this->getIfPart($adapter);

        $select->from(
            ['sales_order' => $this->resourceConnection->getTableName('sales_order')],
            [
                new \Zend_Db_Expr($ifPart), // retrieve result of validation
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

        if ($this->dateRange) {
            $this->dateRange->limitByDateRange($select, 'sales_order.created_at');
        }

        return $adapter->fetchOne($select);
    }

    /**
     * @todo We can validate concrete number of orders like in FUE in Condition\Product\Subselect
     *
     * {@inheritDoc}
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        \Magento\Framework\Profiler::start(__METHOD__);
        if (!$this->dateRange) {
            foreach ($this->getConditions() as $condition) {
                /** @var mixed $condition */
                if (preg_match('/Daterange/', $condition->getType())) {
                    $this->dateRange = $condition;
                    break;
                }
            }
        }
        // Return false if Sales Amount of all orders is not valid from start
        if (!$this->getAttributeValue($this->resourceConnection->getConnection(), $model)) {
            return false;
        }

        // Otherwise validate all orders and combine conditions
        $this->searchCriteriaBuilder->addFilter('store_id', $model->getData('store_id'));
        if ($model->getData('customer_id')) {
            $this->searchCriteriaBuilder->addFilter('customer_id', $model->getData('customer_id'));
        } else {
            $this->searchCriteriaBuilder->addFilter('customer_email', $model->getData('email'));
        }

        $orderList = $this->orderRepository->getList($this->searchCriteriaBuilder->create());

        $validOrderIds          = [];
        $isValidChildConditions = false;
        // implement in later version to validate concrete number of orders
        /*$total                  = 0;
        $count                  = $orderList->getTotalCount();
        $aggregator             = $this->getData('aggregator');
        if ($aggregator != 'any') {
            $this->setData('aggregator', 'all');
        }*/

        // Iterate through all orders and validate them
        /** @var \Magento\Framework\Model\AbstractModel $order */
        foreach ($orderList->getItems() as $order) {
            // without ```parent::validate($order)``` condition Numder of orders > 0 with Date range
            // working also for customers with orders made not during date range
            // but can have effect on other conditions
            if ($this->validateOrder($order) && $this->validateByDateRange($order)) {
                // Collect valid orders
                $validOrderIds[] = $order->getId();
                $isValidChildConditions = true;
                //$total++;
            }
        }

        // set child conditions to true when no child conditions exist (need when no orders exist)
        if (!$this->getConditions()) {
            $isValidChildConditions = true;
        }

        // revalidate child conditions (fixing issue when no orders present but date range condition is present)
        //$isValidChildConditions = $this->revalidateChildConditions($validOrderIds);

        // Set matched order IDs to model for later use
        $model->setData('matched_order_ids', $validOrderIds);

        // Validate Sales Amount only for matched orders
        $isValidSalesAmount = $this->getAttributeValue($this->resourceConnection->getConnection(), $model);

        // to validate concrete number of orders
        /*if ($aggregator == 'any') {
            return $isValidChildConditions && $isValidSalesAmount;
        } elseif ($aggregator == 'all') {
            return ($total == $count) && $isValidSalesAmount;
        } else {
            return ($total == $aggregator) && $isValidSalesAmount;
        }*/
        \Magento\Framework\Profiler::stop(__METHOD__);

        // Combine validation with child conditions
        return $isValidChildConditions && $isValidSalesAmount;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $order
     * @return bool
     */
    public function validateOrder($order)
    {
        // need to reverse validation of the order because when condition value = 0 and child condition present,
        // parent condition value used as aggregator value (0 => all (any) NOT match)
        return $this->getData('aggregator') && $this->getValue() == 0 && $this->getConditions()
            ? !parent::validate($order)
            : parent::validate($order);
    }

    /**
     * @param AbstractModel $model
     * @return bool
     */
    public function validateByDateRange(AbstractModel $model)
    {
        return !$this->dateRange ? true : $this->dateRange->validate($model);
    }
}
