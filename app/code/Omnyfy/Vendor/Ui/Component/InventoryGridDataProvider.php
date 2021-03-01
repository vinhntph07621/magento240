<?php
/**
 * Project: Vendor.
 * User: jing
 * Date: 25/1/18
 * Time: 12:01 PM
 */
namespace Omnyfy\Vendor\Ui\Component;

class InventoryGridDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Api\Search\ReportingInterface $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = [])
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $reporting, $searchCriteriaBuilder, $request, $filterBuilder, $meta, $data);
    }

    protected function searchResultToOutput(\Magento\Framework\Api\Search\SearchResultInterface $searchResult) {
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

        $locationId = $this->request->getParam('id');
        $collection->addFieldToFilter('inventory.location_id', $locationId);

        /*
        $vendorInfo = $this->_backendSession->getVendorInfo();

        if (!empty($vendorInfo)) {
            $collection->addFieldToFilter('vendor_id', $vendorInfo['vendor_id']);
        }
        */

        return $this->searchResultToOutput($collection);
    }
}