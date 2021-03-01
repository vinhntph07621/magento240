<?php

namespace Omnyfy\Mcm\Ui\DataProvider\MarketplaceReport;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Magento\Framework\Api\Search\SearchResultInterface;
use Omnyfy\Mcm\Helper\Data as HelperData;

class VendorReportDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider {

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricing;
    
    /**
     * @var Omnyfy\Mcm\Model\ResourceModel\VendorFeeReport
     */
    protected $vendorFeeReportResource;

    /**
     * @param string                $name
     * @param string                $primaryFieldName
     * @param string                $requestFieldName
     * @param Reporting             $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface      $request
     * @param FilterBuilder         $filterBuilder
     * @param \Omnyfy\Mcm\Model\ResourceModel\VendorFeeReport $vendorFeeReportResource
     * @param array                 $meta
     * @param array                 $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
    $name, $primaryFieldName, $requestFieldName, Reporting $reporting, SearchCriteriaBuilder $searchCriteriaBuilder,
            RequestInterface $request, FilterBuilder $filterBuilder,
            \Magento\Framework\Pricing\Helper\Data $pricing,
            \Omnyfy\Mcm\Model\ResourceModel\VendorFeeReport $vendorFeeReportResource, HelperData $helper,
            array $meta = [], array $data = []
    ) {
        $this->pricing = $pricing;
        $this->request = $request;
        $this->vendorFeeReportResource = $vendorFeeReportResource;
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

    public function getData() {

        $collection = $this->getSearchResult();

        foreach ($collection as $report) {

            $report->setData('total_category_commission', ($report['total_category_commission']) ? $this->currency($report['total_category_commission']) : 'A$0.00');
            $report->setData('total_seller_fee', ($report['total_seller_fee']) ? $this->currency($report['total_seller_fee']) : 'A$0.00');
            $report->setData('total_disbursement_fee', ($report['total_disbursement_fee']) ? $this->currency($report['total_disbursement_fee']) : 'A$0.00');
            $report->setData('gross_earnings', ($report['gross_earnings']) ? $this->currency($report['gross_earnings']) : 'A$0.00');
        }
        $collection->getSelect();

        return $this->searchResultToOutput($collection);
    }
    
    public function currency($value) {
        return $this->_helper->formatToBaseCurrency($value);
    }
}
