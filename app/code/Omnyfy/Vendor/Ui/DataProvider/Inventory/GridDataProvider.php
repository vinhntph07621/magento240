<?php
/**
 * Project: Multi Vendors.
 * User: jing
 * Date: 30/1/18
 * Time: 1:33 PM
 */
namespace Omnyfy\Vendor\Ui\DataProvider\Inventory;
use Psr\Log\LoggerInterface;
class GridDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    protected $_backendSession;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Api\Search\ReportingInterface $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Backend\Model\Session $backendSession,
        array $meta = [],
        array $data = [])
    {
        $this->logger = $logger;
        $this->_backendSession = $backendSession;
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

        $this->logger->debug(sprintf('load location inventory for location id (from param): %s ', $locationId));

        if (empty($locationId)) {
            $locationId = $this->_backendSession->getCurrentLocationId();
            $this->logger->debug(sprintf('load location inventory for location id (from backend session): %s ', $locationId));
        }

        $collection->addFieldToFilter('location_id', $locationId);

        /*
        $vendorInfo = $this->_backendSession->getVendorInfo();

        if (!empty($vendorInfo)) {
            $collection->addFieldToFilter('vendor_id', $vendorInfo['vendor_id']);
        }
        */

        $this->logger->debug(sprintf('location inventory searchResultToOutput result %s ', $collection->getSize()));

        return $this->searchResultToOutput($collection);
    }
}