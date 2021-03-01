<?php

namespace Omnyfy\Mcm\Ui\DataProvider\MarketplaceReport;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Magento\Framework\Api\Search\SearchResultInterface;
use Omnyfy\Mcm\Model\FeesChargesFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class CommissionReportDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider {

    protected $pricing;
    
    /**
     * @var Omnyfy\Mcm\Model\ResourceModel\FeesManagement
     */
    protected $feesManagementResource;
    
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /*
     * @var \Omnyfy\Mcm\Helper\Data
     */
    protected $_helper;
    
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * @param string                $name
     * @param string                $primaryFieldName
     * @param string                $requestFieldName
     * @param Reporting             $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface      $request
     * @param FilterBuilder         $filterBuilder
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Omnyfy\Mcm\Model\ResourceModel\FeesManagement $feesManagementResource
     * @param PriceCurrencyInterface $priceFormatter
     * @param \Omnyfy\Mcm\Helper\Data 
     * @param array                 $meta
     * @param array                 $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
    $name, $primaryFieldName, $requestFieldName, Reporting $reporting, SearchCriteriaBuilder $searchCriteriaBuilder,
            RequestInterface $request, FilterBuilder $filterBuilder,
            \Magento\Framework\Pricing\Helper\Data $pricing, FeesChargesFactory $feesChargesFactory,
            \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
            \Omnyfy\Mcm\Model\ResourceModel\FeesManagement $feesManagementResource,
            PriceCurrencyInterface $priceFormatter,
            \Omnyfy\Mcm\Helper\Data $helper,
            array $meta = [], array $data = []
    ) {
        $this->pricing = $pricing;
        $this->request = $request;
        $this->priceFormatter = $priceFormatter;
        $this->feesChargesFactory = $feesChargesFactory->create();
        $this->feesManagementResource = $feesManagementResource;
        $this->_helper = $helper;
        parent::__construct(
                $name, $primaryFieldName, $requestFieldName, $reporting, $searchCriteriaBuilder, $request, $filterBuilder, $meta, $data
        );
    }

    /**
     * @param SearchResultInterface $searchResult
     * @return array
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult) {
        $arrItems = [];
        $arrItems['totalRecords'] = $searchResult->getTotalCount();

        $arrItems['items'] = [];
        foreach ($searchResult->getItems() as $item) {
            $arrItems['items'][] = $item->getData();
        }

        return $arrItems;
    }

    /**
     * {@inheritdoc}
     */
    public function getData() {

        $collection = $this->getSearchResult();
        
        foreach ($collection as $report) {
            $currencyCode = isset($report['base_currency_code']) ? $report['base_currency_code'] : null;
            
            $report->setData('increment_id', ($report['increment_id']) ? $report['increment_id'] : '');
            $report->setData('created_at', ($report['created_at']) ? $report['created_at'] : '');
            $report->setData('shipping_incl_tax', ($report['shipping_incl_tax']) ? $this->priceFormatter->format($report['shipping_incl_tax'], false, null, null, $currencyCode) : 'A$0.00');
            $report->setData('discount_amount', ($report['discount_amount']) ? $this->priceFormatter->format($report['discount_amount'], false, null, null, $currencyCode) : 'A$0.00');
            $report->setData('grand_total', ($report['grand_total']) ? $this->priceFormatter->format($report['grand_total'], false, null, null, $currencyCode) : 'A$0.00');
            $report->setData('category_fee', ($report['category_fee']) ? $this->priceFormatter->format($report['category_fee'], false, null, null, $currencyCode) : 'A$0.00');
            $report->setData('seller_fee', ($report['seller_fee']) ? $this->priceFormatter->format($report['seller_fee'], false, null, null, $currencyCode) : 'A$0.00');
            $report->setData('disbursement_fee', ($report['disbursement_fee']) ? $this->priceFormatter->format($report['disbursement_fee'], false, null, null, $currencyCode) : 'A$0.00');
            $report->setData('mcm_transaction_fee_incl_tax', ($report['mcm_transaction_fee_incl_tax']) ? $this->priceFormatter->format($report['mcm_transaction_fee_incl_tax'], false, null, null, $currencyCode) : 'A$0.00');
            $report->setData('gross_earnings', ($report['gross_earnings']) ? $this->priceFormatter->format($report['gross_earnings'], false, null, null, $currencyCode): 'A$0.00');
        }
        $collection->getSelect();
        
        return $this->searchResultToOutput($collection);
    }

}
