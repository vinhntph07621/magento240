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
use Magento\Rule\Model\Condition\Context;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Mirasvit\CustomerSegment\Api\Service\OperatorConversionInterface;

class Lastorder extends AbstractSales
{
    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * Lastorder constructor.
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param OperatorConversionInterface $operatorConverter
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param ResourceConnection $resourceConnection
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        OrderCollectionFactory $orderCollectionFactory,
        OperatorConversionInterface $operatorConverter,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderRepositoryInterface $orderRepository,
        ResourceConnection $resourceConnection,
        Context $context,
        array $data = []
    ) {
        parent::__construct(
            $operatorConverter,
            $searchCriteriaBuilder,
            $orderRepository,
            $resourceConnection,
            $context,
            $data
        );

        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected $_inputType = 'numeric';

    /**
     * Get HTML of condition string.
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __(
                'Last Order placed %1 %2 days ago while %3 of these Conditions match:',
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
        return __(
            'Last Order placed %1 %2 days ago while %3 of these Conditions match:',
            $this->getOperatorName(),
            $this->getValueName(),
            $this->getAggregatorName()
        );
    }

    /**
     * @inheritDoc
     */
    public function getIfPart(AdapterInterface $adapter)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $order = null;

        $orders = $this->orderCollectionFactory->create();
        // issue with customer created in one storeview and orders placed in another
//        $orders->addFieldToFilter('store_id', $model->getData('store_id'));
        $orders->getSelect()
            ->order(OrderInterface::CREATED_AT . ' desc')
            ->limit(1);

        if ($model->getData('customer_id')) {
            $orders->addFieldToFilter('customer_id', $model->getData('customer_id'));
        } else {
            $orders->addFieldToFilter('customer_email', $model->getData('email'));
        }

        // Validate order over child conditions
        /** @var \Magento\Sales\Model\Order $item */
        foreach ($orders as $item) {
            if ($this->_isValid($item)) {
                $order = $item;
            }
        }

        if (!$order) {
            return false;
        }

        return $this->validateAttribute($this->getOrderDaysAgo($order));
    }

    /**
     * Get number of days passed since order placement.
     *
     * @param OrderInterface $order
     *
     * @return float
     */
    private function getOrderDaysAgo(OrderInterface $order)
    {
        return round((time() - strtotime($order->getCreatedAt())) / (60 * 60 * 24));
    }
}
