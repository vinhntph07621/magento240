<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-09
 * Time: 10:34
 */
namespace Omnyfy\VendorSubscription\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class SubscriptionActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    protected $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = [])
    {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach($dataSource['data']['items'] as &$item) {
            $item[$this->getData('name')]['edit'] = [
                'href' => $this->urlBuilder->getUrl(
                    'omnyfy_subscription/subscription/edit',
                    ['id' => $item['id']]
                ),
                'label' => __('Edit'),
                'hidden' => false,
            ];
        }

        return $dataSource;
    }
}
 