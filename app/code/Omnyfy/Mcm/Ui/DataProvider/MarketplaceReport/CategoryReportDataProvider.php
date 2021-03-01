<?php

namespace Omnyfy\Mcm\Ui\DataProvider\MarketplaceReport;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Magento\Framework\Api\Search\SearchResultInterface;

class CategoryReportDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider {

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
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
     * @param array                 $meta
     * @param array                 $data
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceFormatter 
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
    $name, $primaryFieldName, $requestFieldName, Reporting $reporting, SearchCriteriaBuilder $searchCriteriaBuilder,
            RequestInterface $request, FilterBuilder $filterBuilder,
            \Magento\Framework\Pricing\PriceCurrencyInterface $priceFormatter, 
            array $meta = [], array $data = []
    ) {
        $this->priceFormatter = $priceFormatter;
        $this->request = $request;
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
            $currencyCode = isset($report['base_currency_code']) ? $report['base_currency_code'] : null;
            $report->setData('category_id', ($report['category_id']) ? $report['category_id'] : '');
            $report->setData('category_name', ($report['category_name']) ?  $report['category_name'] : '');
            $report->setData('category_commission_percentage', ($report['category_commission_percentage']) ? $report['category_commission_percentage'] .'%' : '');
            $report->setData('category_commission_earned', ($report['category_commission_earned']) ? $this->priceFormatter->format($report['category_commission_earned'], false, null, null, $currencyCode) : '');
            
        }
        $collection->getSelect();

        return $this->searchResultToOutput($collection);
    }
}
