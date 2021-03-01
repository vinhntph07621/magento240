<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-09
 * Time: 10:58
 */
namespace Omnyfy\VendorSubscription\Observer;

class AddCancelSubscriptionButton implements \Magento\Framework\Event\ObserverInterface
{
    protected $registry;

    protected $urlBuilder;

    protected $subscriptionCollectionFactory;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Omnyfy\VendorSubscription\Model\Resource\Subscription\CollectionFactory $collectionFactory
    ) {
        $this->registry = $registry;
        $this->urlBuilder = $urlBuilder;
        $this->subscriptionCollectionFactory = $collectionFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $buttonList = $observer->getData('button_list');

        //load current vendor from registry, do nothing if no current vendor set
        $vendor = $this->registry->registry('current_omnyfy_vendor_vendor');
        if (empty($vendor) || empty($vendor->getId())) {
            return;
        }

        //load subscription status by vendor id, if subscription status is active, then show button.
        $subscription = $this->getSubscriptionByVendorId($vendor->getId());
        if (empty($subscription) || \Omnyfy\VendorSubscription\Model\Source\SubscriptionStatus::STATUS_ACTIVE != $subscription->getStatus()) {
            return;
        }

        //generate url with vendor id
        $url = $this->urlBuilder->getUrl('omnyfy_subscription/subscription/cancel',
            [
                'id' => $subscription->getId(),
                'vendor_id' => $vendor->getId(),
            ]
        );

        $buttonList->add(
            'cancel_subcription',
            [
                'label' => __('Cancel Subscription'),
                'on_click' => sprintf("location.href = '%s';", $url),
            ]
        );
    }

    protected function getSubscriptionByVendorId($vendorId)
    {
        $collection = $this->subscriptionCollectionFactory->create();
        $collection->addFieldToFilter('vendor_id', $vendorId)
            ->setPageSize(1)
        ;

        $count = $collection->getSize();
        if (!$count) {
            return false;
        }

        return $collection->getFirstItem();
    }
}
 