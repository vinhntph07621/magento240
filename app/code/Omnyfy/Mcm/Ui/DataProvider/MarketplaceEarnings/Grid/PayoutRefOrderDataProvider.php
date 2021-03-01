<?php

namespace Omnyfy\Mcm\Ui\DataProvider\MarketplaceEarnings\Grid;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Magento\Framework\Api\Search\SearchResultInterface;
use Omnyfy\Mcm\Model\ResourceModel\VendorPayout;
use Omnyfy\Mcm\Helper\Data as HelperData;

class PayoutRefOrderDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider {

    protected $pricing;

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
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
    $name, $primaryFieldName, $requestFieldName, Reporting $reporting, SearchCriteriaBuilder $searchCriteriaBuilder, RequestInterface $request, FilterBuilder $filterBuilder, \Magento\Framework\Pricing\Helper\Data $pricing, VendorPayout $vendorPayoutResource, HelperData $helper, array $meta = [], array $data = []
    ) {
        $this->pricing = $pricing;
        $this->vendorPayoutResource = $vendorPayoutResource;
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

        foreach ($collection as $payout) {
            $payout->setData('vendor_total_incl_tax', $this->currency($payout['vendor_total_incl_tax']));
            $payout->setData('total_category_fee_incl_tax', $this->currency($payout['total_category_fee_incl_tax']));
            $payout->setData('total_seller_fee_incl_tax', $this->currency($payout['total_seller_fee_incl_tax']));
            $payout->setData('total_disbursement_fee_incl_tax', $this->currency($payout['total_disbursement_fee_incl_tax']));
            $payout->setData('payout_amount', $this->currency($payout['payout_amount']));
        }

        return $this->searchResultToOutput($collection);
    }
    
    public function currency($value) {
        return $this->_helper->formatToBaseCurrency($value);
    }
    
}
