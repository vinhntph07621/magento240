<?php
namespace Omnyfy\VendorGallery\Ui\Component;

class AlbumGridDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * AlbumGridDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magento\Framework\Api\Search\ReportingInterface $reporting
     * @param \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Backend\Model\Session $backendSession
     * @param array $meta
     * @param array $data
     */
    public function __construct(
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
        $this->backendSession = $backendSession;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $reporting, $searchCriteriaBuilder, $request, $filterBuilder, $meta, $data);
    }

    protected function searchResultToOutput(\Magento\Framework\Api\Search\SearchResultInterface $searchResult) {
        $arrItems = [];
        $arrItems['totalRecords'] = $searchResult->getTotalCount();

        $arrItems['items'] = [];
        foreach ($searchResult->getItems() as $item) {
            $item->setData('locations', $item->getAllLocationIds());
            $arrItems['items'][] = $item->getData();
        }

        return $arrItems;
    }

    public function getData()
    {
        $collection = $this->getSearchResult();
        $vendorInfo = $this->backendSession->getVendorInfo();
        if (!empty($vendorInfo) && isset($vendorInfo['vendor_id'])) {
            $vendorId = $vendorInfo['vendor_id'];
            $collection->addFieldToFilter('vendor_id', $vendorId);
        }

        return $this->searchResultToOutput($collection);
    }
}
