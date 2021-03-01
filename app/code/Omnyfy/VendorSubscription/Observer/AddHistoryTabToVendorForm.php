<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-11
 * Time: 17:26
 */
namespace Omnyfy\VendorSubscription\Observer;

class AddHistoryTabToVendorForm implements \Magento\Framework\Event\ObserverInterface
{
    protected $urlBuilder;

    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $tabs = $observer->getData('tabs');

        $tabs->addTab('history',
            [
                'label' => __('Subscription History'),
                'title' => __('Subscription History'),
                'url' => $this->getUrl('omnyfy_subscription/history/grid', ['_current' => true]),
                'class' => 'ajax'
            ]
        );
    }

    protected function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}