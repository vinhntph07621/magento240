<?php

namespace Omnyfy\Mcm\Ui\DataProvider\MarketplaceReport;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Magento\Framework\Api\Search\SearchResultInterface;
use Omnyfy\Mcm\Model\FeesChargesFactory;
use Omnyfy\Mcm\Helper\Data as HelperData;

class DetailReportDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider {

    protected $pricing;
    
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;
    
    /**
     * @var Omnyfy\Mcm\Model\ResourceModel\FeesManagement
     */
    protected $feesManagementResource;
    
    /**
     * @var Magento\Sales\Model\Order\Item
     */
    protected $orderItem;
    
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
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @param \Omnyfy\Mcm\Model\MarketplaceDetailedReportFactory $_marketplaceReportFactory
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
            \Magento\Sales\Model\Order\Item $orderItem,
            \Magento\Backend\Model\Auth\Session $adminSession,
            \Omnyfy\Mcm\Model\MarketplaceDetailedReportFactory $_marketplaceReportFactory, HelperData $helper,
            array $meta = [], array $data = []
    ) {
        $this->pricing = $pricing;
        $this->request = $request;
        $this->orderRepository = $orderRepository;
        $this->feesChargesFactory = $feesChargesFactory->create();
        $this->feesManagementResource = $feesManagementResource;
        $this->orderItem = $orderItem;
        $this->_marketplaceReportFactory = $_marketplaceReportFactory;
        $this->_adminSession = $adminSession;
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
            $report->setData('price_paid', ($report['price_paid']) ? $this->currency($report['price_paid']) : '');
            $report->setData('shipping_and_hanldling_total', ($report['shipping_and_hanldling_total']) ? $this->currency($report['shipping_and_hanldling_total']) : '');
            $report->setData('discount', ($report['discount']) ? $this->currency($report['discount']) : '');
            $report->setData('order_total_value', ($report['order_total_value']) ? $this->currency($report['order_total_value']) : '');
            $report->setData('category_commission', ($report['category_commission']) ? $this->currency($report['category_commission']) : '');
            $report->setData('seller_fee', ($report['seller_fee']) ? $this->currency($report['seller_fee']) : '');
            $report->setData('disbursement_fee', ($report['disbursement_fee']) ? $this->currency($report['disbursement_fee']) : '');
            $report->setData('transaction_fees', ($report['transaction_fees']) ? $this->currency($report['transaction_fees']) : '');
            $report->setData('gross_earnings', ($report['gross_earnings']) ? $this->currency($report['gross_earnings']) : '');
            
        }
        $collection->getSelect();

        return $this->searchResultToOutput($collection);
    }
    
    
    public function currency($value) {
        return $this->_helper->formatToBaseCurrency($value);
    }

}
