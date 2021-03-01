<?php
namespace Omnyfy\Cms\Ui\DataProvider\Country\Grid;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Magento\Framework\Api\Search\SearchResultInterface;
use Omnyfy\Cms\Model\ResourceModel\Country\CollectionFactory;

class CountryDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider {

    protected $objectManager;
    protected $countryCollection;
    protected $authSession;

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
    $name, $primaryFieldName, $requestFieldName, Reporting $reporting, SearchCriteriaBuilder $searchCriteriaBuilder, RequestInterface $request, FilterBuilder $filterBuilder, \Magento\Framework\ObjectManagerInterface $objectManager, CollectionFactory $countryCollectionFactory, \Magento\Backend\Model\Auth\Session $authSession, array $meta = [], array $data = []
    ) {
        $this->objectManager = $objectManager;
        $this->countryCollection = $countryCollectionFactory->create();
        $this->authSession = $authSession;

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
        /** @var \Omnyfy\Cms\Model\ResourceModel\Country\Collection $collection */
        $collection = $this->getSearchResult();

        $collection
                ->addFieldToSelect([
                    'id',
                    'country_id',
                    'country_name',
                    'identifier',
                    'status',
        ]);

        $data = $this->searchResultToOutput($collection);
        return $data;
    }

}
