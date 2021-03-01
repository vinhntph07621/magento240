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

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\Context;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use Mirasvit\CustomerSegment\Model\Segment\Condition\AbstractCondition;

class LastShipment extends AbstractCondition
{
    /**
     * @var ShipmentCollectionFactory
     */
    private $shipmentCollectionFactory;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * LastShipment constructor.
     * @param ShipmentCollectionFactory $shipmentCollectionFactory
     * @param ResourceConnection $resource
     * @param Context $context
     * @param array $data
     * @throws \Exception
     */
    public function __construct(
        ShipmentCollectionFactory $shipmentCollectionFactory,
        ResourceConnection $resource,
        Context $context,
        array $data = []
    ) {
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->resource                  = $resource;

        $data['label'] = __('Last Shipping Date');

        parent::__construct($context, $data);
    }

    /**
     * @return array|mixed
     */
    public function getAttributeOption()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . __(
                'Last Shipping Date was %1 %2 days ago',
                $this->getOperatorElementHtml(),
                $this->getValueElementHtml()
            )
            . $this->getRemoveLinkHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function asString($format = '')
    {
        return __(
            'Last Shipping Date was %1 %2 days ago',
            $this->getOperatorName(),
            $this->getValueName()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function validate(AbstractModel $model)
    {
        $order = null;

        $shipments = $this->shipmentCollectionFactory->create();
        // issue with customer created in one storeview and orders placed in another
//        $shipments->addFieldToFilter('main_table.store_id', $model->getData('store_id'));
        $shipments->getSelect()
            ->order('main_table.created_at desc')
            ->limit(1);

        $shipments->getSelect()
            ->joinLeft(
                ['o' => $this->resource->getTableName('sales_order')],
                'main_table.order_id = o.entity_id'
            );

        if ($model->getData('customer_id')) {
            $shipments->addFieldToFilter('main_table.customer_id', $model->getData('customer_id'));
        } else {
            $shipments->addFieldToFilter('o.customer_email', $model->getData('email'));
        }

        /** @var ShipmentInterface $shipment */
        $shipment = $shipments->getLastItem();

        if (!$shipment->getId()) {
            return false;
        }

        return $this->validateAttribute($this->getDaysAgo($shipment));
    }

    /**
     * @param ShipmentInterface $shipment
     * @return float
     */
    private function getDaysAgo(ShipmentInterface $shipment)
    {
        return round((time() - strtotime($shipment->getCreatedAt())) / (60 * 60 * 24));
    }
}
