<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-01
 * Time: 16:56
 */
namespace Omnyfy\VendorSubscription\Ui\DataProvider\Plan\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class Main implements ModifierInterface
{
    protected $locator;

    protected $usageResource;

    public function __construct(
        \Omnyfy\VendorSubscription\Model\Plan\Locator\LocatorInterface $locator,
        \Omnyfy\VendorSubscription\Model\Resource\Usage $usageResource
    )
    {
        $this->locator = $locator;
        $this->usageResource = $usageResource;
    }

    public function modifyData(array $data)
    {
        $plan = $this->locator->getPlan();
        $id = $plan->getId();

        if (!empty($plan)) {
            $data[$id] = $plan->getData();
            $data[$id]['plan'] = $plan->getData();
            $data[$id]['id'] = $id;
            $data[$id]['plan']['id'] = $id;

            $planUsage = $this->usageResource->loadPlanUsageRelation($id);
            if (!empty($planUsage)) {
                $usageData = [];
                foreach($planUsage as $typeId => $limit) {
                    $usageData[] = [
                        'usage_type_id' => $typeId,
                        'usage_limit' => $limit
                    ];
                }
                $data[$id]['plan_usage'] = $usageData;
            }
        }
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
 