<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 11/9/19
 * Time: 10:37 am
 */
namespace Omnyfy\VendorSubscription\Ui\DataProvider\Subscription\Update\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class Main implements ModifierInterface
{
    protected $locator;

    protected $subLocator;

    protected $helper;

    protected $urlBuilder;

    public function __construct(
        \Omnyfy\VendorSubscription\Model\Update\Locator\LocatorInterface $locator,
        \Omnyfy\VendorSubscription\Model\Subscription\Locator\LocatorInterface $subLocator,
        \Omnyfy\VendorSubscription\Helper\Data $helper,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->locator = $locator;
        $this->subLocator = $subLocator;
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
    }

    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    public function modifyData(array $data)
    {
        $update = $this->locator->getUpdate();
        $subscription = $this->subLocator->getSubscription();
        $id = $update->getId();

        if (!empty($subscription->getId())) {
            $data[$id]['subscription_id'] = $subscription->getId();
            if (empty($update->getId())){
                $data[$id]['vendor_id'] = $subscription->getVendorId();
                $data[$id]['from_plan_id'] = $subscription->getPlaneId();
                $data[$id]['from_plan_name'] = $subscription->getPlanName();
            }
            else{
                $data[$id] = $update->getData();
            }

            $parameters = [
                'id' => $subscription->getId()
            ];

            $submitUrl = $this->urlBuilder->getUrl('omnyfy_subscription/subscription_update/save', $parameters);

            $data = array_replace_recursive(
                $data,
                [
                    'config' => [
                        'submit_url' => $submitUrl,
                    ]
                ]
            );
        }

        return $data;
    }
}
 