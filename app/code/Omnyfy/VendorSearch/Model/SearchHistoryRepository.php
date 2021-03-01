<?php


namespace Omnyfy\VendorSearch\Model;

use Omnyfy\VendorSearch\Api\SearchHistoryRepositoryInterface;
use Omnyfy\VendorSearch\Model\ResourceModel\SearchHistory as ResourceSearchHistory;
use Omnyfy\VendorSearch\Model\ResourceModel\SearchHistory\CollectionFactory as SearchHistoryCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Omnyfy\VendorSearch\Api\Data\SearchHistoryInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SortOrder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Omnyfy\VendorSearch\Api\Data\SearchHistorySearchResultsInterfaceFactory;

class SearchHistoryRepository implements SearchHistoryRepositoryInterface
{

    protected $resource;

    protected $dataSearchHistoryFactory;

    protected $dataObjectProcessor;

    protected $searchHistoryFactory;

    protected $searchHistoryCollectionFactory;

    private $storeManager;

    protected $dataObjectHelper;

    protected $searchResultsFactory;


    /**
     * @param ResourceSearchHistory $resource
     * @param SearchHistoryFactory $searchHistoryFactory
     * @param SearchHistoryInterfaceFactory $dataSearchHistoryFactory
     * @param SearchHistoryCollectionFactory $searchHistoryCollectionFactory
     * @param SearchHistorySearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceSearchHistory $resource,
        SearchHistoryFactory $searchHistoryFactory,
        SearchHistoryInterfaceFactory $dataSearchHistoryFactory,
        SearchHistoryCollectionFactory $searchHistoryCollectionFactory,
        SearchHistorySearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->searchHistoryFactory = $searchHistoryFactory;
        $this->searchHistoryCollectionFactory = $searchHistoryCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataSearchHistoryFactory = $dataSearchHistoryFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface $searchHistory
    ) {
        /* if (empty($searchHistory->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $searchHistory->setStoreId($storeId);
        } */
        try {
            $this->resource->save($searchHistory);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the searchHistory: %1',
                $exception->getMessage()
            ));
        }
        return $searchHistory;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($searchHistoryId)
    {
        $searchHistory = $this->searchHistoryFactory->create();
        $this->resource->load($searchHistory, $searchHistoryId);
        if (!$searchHistory->getId()) {
            throw new NoSuchEntityException(__('SearchHistory with id "%1" does not exist.', $searchHistoryId));
        }
        return $searchHistory;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->searchHistoryCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $fields[] = $filter->getField();
                $condition = $filter->getConditionType() ?: 'eq';
                $conditions[] = [$condition => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
        
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface $searchHistory
    ) {
        try {
            $this->resource->delete($searchHistory);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the SearchHistory: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($searchHistoryId)
    {
        return $this->delete($this->getById($searchHistoryId));
    }
}
