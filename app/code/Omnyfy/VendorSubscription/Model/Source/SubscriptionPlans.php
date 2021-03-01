<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-10
 * Time: 11:56
 */
namespace Omnyfy\VendorSubscription\Model\Source;

use Omnyfy\VendorSubscription\Model\Source\Status as PlanStatus;

class SubscriptionPlans extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected $collectionFactory;

    protected $values;

    public function __construct(
        \Omnyfy\VendorSubscription\Model\Resource\Plan\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    public function toValuesArray()
    {
        if (null == $this->values) {
            $result = [];
            $collection = $this->collectionFactory->create();
            //Should include all plans even disabled and hidden on front
            //$collection->addFieldToFilter('status', PlanStatus::STATUS_ACTIVE);
            foreach($collection as $type) {
                $result[$type->getId()] = $type->getPlanName();
            }
            $this->values = $result;
        }
        return $this->values;
    }

    public function getAllOptions()
    {
        $result = [];
        $values = $this->toValuesArray();
        foreach($values as $id => $name) {
            $result[] = [
                'value' => $id,
                'label' => $name
            ];
        }
        return $result;
    }
}
 