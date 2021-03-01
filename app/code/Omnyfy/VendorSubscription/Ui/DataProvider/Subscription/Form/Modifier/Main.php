<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-09
 * Time: 16:04
 */
namespace Omnyfy\VendorSubscription\Ui\DataProvider\Subscription\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class Main implements ModifierInterface
{
    protected $locator;

    public function __construct(\Omnyfy\VendorSubscription\Model\Subscription\Locator\LocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    public function modifyData(array $data)
    {
        $subscription = $this->locator->getSubscription();
        $id = $subscription->getId();

        if (!empty($subscription)) {
            $data[$id] = $subscription->getData();
            $data[$id]['subscription'] = $subscription->getData();
            $data[$id]['id'] = $id;
            $data[$id]['subscription']['id'] = $id;

            if (!isset($data[$id]['subscription']['status'])) {
                $data[$id]['subscription']['status'] = '1';
            }
        }

        return $data;
    }

    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
 