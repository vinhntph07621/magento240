<?php

namespace Omnyfy\Mcm\Ui\DataProvider\MarketplaceReport;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Magento\Framework\Api\Search\SearchResultInterface;
use Omnyfy\Mcm\Model\FeesChargesFactory;
use Omnyfy\Mcm\Helper\Data as HelperData;

class FeeVendorReportDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider {

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
     * @var \Omnyfy\Mcm\Model\VendorFeeReportAdminFactory
     */
    protected $_vendorFeeReportAdminFactory;
    
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
     * @param \Omnyfy\Mcm\Model\VendorFeeReportAdminFactory $_vendorFeeReportAdminFactory
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
            \Omnyfy\Mcm\Model\VendorFeeReportAdminFactory $_vendorFeeReportAdminFactory, HelperData $helper,
            array $meta = [], array $data = []
    ) {
        $this->pricing = $pricing;
        $this->request = $request;
        $this->orderRepository = $orderRepository;
        $this->feesChargesFactory = $feesChargesFactory->create();
        $this->feesManagementResource = $feesManagementResource;
        $this->orderItem = $orderItem;
        $this->_vendorFeeReportAdminFactory = $_vendorFeeReportAdminFactory;
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
            $currencyCode = isset($order['base_currency_code']) ? $order['base_currency_code'] : null;

            $report->setData('order_id', ($report['order_id']) ? $report['order_id'] : '');
            $report->setData('product_sku', ($report['product_sku']) ? $report['product_sku'] : '');
            $report->setData('product_name', ($report['product_name']) ? $report['product_name'] : '');
            $report->setData('price_paid', ($report['price_paid']) ? $this->currency($report['price_paid']) : '');
            $report->setData('shipping_and_hanldling_total', ($report['shipping_and_hanldling_total']) ? $this->currency($report['shipping_and_hanldling_total']) : '');
            $report->setData('discount', ($report['discount']) ? $this->currency($report['discount']) : '');
            $report->setData('order_total_value', ($report['order_total_value']) ? $this->currency($report['order_total_value']) : '');
            $report->setData('category_commission', ($report['category_commission']) ? $this->currency($report['category_commission']) : '');
            $report->setData('seller_fee', ($report['seller_fee']) ? $this->currency($report['seller_fee']) : '');
            $report->setData('disbursement_fee', ($report['disbursement_fee']) ? $this->currency($report['disbursement_fee']) : '');
            $report->setData('total_fee', ($report['total_fee']) ? $this->currency($report['total_fee']) : '');
            $report->setData('gross_earnings', ($report['gross_earnings']) ? $this->currency($report['gross_earnings']) : '');
            $report->setData('tax', ($report['tax']) ? $this->currency($report['tax']) : '');
            $report->setData('net_earnings', ($report['net_earnings']) ? $this->currency($report['net_earnings']) : '');

         }
        $collection->getSelect();

        return $this->searchResultToOutput($collection);
    }
    public function currency($value) {
        return $this->_helper->formatToBaseCurrency($value);
    }

}