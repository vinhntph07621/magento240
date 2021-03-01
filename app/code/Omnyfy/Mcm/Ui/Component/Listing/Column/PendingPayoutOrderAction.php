<?php

namespace Omnyfy\Mcm\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class PendingPayoutOrderAction extends Column {

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
    ContextInterface $context, UiComponentFactory $uiComponentFactory, UrlInterface $urlBuilder, array $components = [], array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {

            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['view'] = [
                    'href' => $this->urlBuilder->getUrl(
                            'sales/order/view', ['order_id' => $item['order_id']]
                    ),
                    'label' => __('View'),
                    'hidden' => false,
                ];
                //if ($item['payout_status'] == 0 && $item['payout_action'] == 1) {
                    $item[$this->getData('name')]['move_to_pending'] = [
                        'href' => $this->urlBuilder->getUrl(
                                'omnyfy_mcm/pendingpayouts/movetopending', ['id' => $item['id'], 'vendor_id' => $item['vendor_id'] ]
                        ),
                        'label' => __('Move to Pending'),
                        'hidden' => false,
                    ];
                //}
            }
        }

        return $dataSource;
    }

}
