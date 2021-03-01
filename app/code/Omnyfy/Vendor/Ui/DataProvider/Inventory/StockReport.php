<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 17/1/20
 * Time: 12:26 pm
 */
namespace Omnyfy\Vendor\Ui\DataProvider\Inventory;

class StockReport extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    protected $_backendSession;

    protected $_resource;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Api\Search\ReportingInterface $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Framework\App\ResourceConnection $resource,
        array $meta = [],
        array $data = [])
    {
        $this->_backendSession = $backendSession;
        $this->_resource = $resource;
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

        $vendorInfo = $this->_backendSession->getVendorInfo();
        if (!empty($vendorInfo) && !$collection->getFlag('has_vendor_filter')) {
            $locationTable = $this->_resource->getTableName('omnyfy_vendor_location_entity');
            $subSql = 'SELECT DISTINCT entity_id FROM ' . $locationTable . ' WHERE vendor_id=?';

            $collection->addFieldToFilter('main_table.location_id',
                [
                    'in' => new \Zend_Db_Expr($this->_resource->getConnection()->quoteInto($subSql, $vendorInfo['vendor_id']))
                ]
            );

            $collection->setFlag('has_vendor_filter', 1);
        }

        return $this->searchResultToOutput($collection);
    }
}
 