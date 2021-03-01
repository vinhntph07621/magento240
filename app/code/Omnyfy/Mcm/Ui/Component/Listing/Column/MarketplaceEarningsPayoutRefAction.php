<?php

namespace Omnyfy\Mcm\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class MarketplaceEarningsPayoutRefAction extends Column {

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
                if (isset($item[$this->getData('name')]) && ($item['payout_status_org'] == 1 || $item['payout_status_org'] == 4)) {
                    $html = "<a href='" . $this->urlBuilder->getUrl('omnyfy_mcm/marketplaceearning/payoutreforder', ['payout_ref' => $item['payout_ref']]) . "'>";
                    $html .= $item['payout_ref'];
                    $html .= "</a>";
                    $item[$this->getData('name')] = $html;
                } else {
					$item[$this->getData('name')] = '';
				}
            }
        }

        return $dataSource;
    }

}
