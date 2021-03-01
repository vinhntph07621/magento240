<?php

namespace Omnyfy\Mcm\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class VendorEarningsPayoutStatus extends Column {

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
                if ($item['payout_status'] == 1) { // showing only in case of completed
					$html = "<a href='" . $this->urlBuilder->getUrl('omnyfy_mcm/payouthistory/vendorInvoice', ['invoice_id' => $item['invoice_id']]) . "'>";
					$html .= $item['invoice_increment_id'];
					$html .= "</a>";
                    $item[$this->getData('name')] = $html;
                    // $item['payout_status'] = '<div class="message message-success">&nbsp;</div>'; 
                } else if($item['payout_status'] == 3 || $item['payout_status'] == 4) {
					$item[$this->getData('name')] = '<span class="fa fa-spinner in-progress-icon">&nbsp;</span>';
                } else {
					$item[$this->getData('name')] = '<span class="fa fa-clock-o pending-icon">&nbsp;</span>';
                }
            }
        }
		return $dataSource;
    }
}