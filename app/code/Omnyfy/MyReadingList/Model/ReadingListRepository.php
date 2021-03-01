<?php


namespace Omnyfy\MyReadingList\Model;

use Omnyfy\MyReadingList\Api\ReadingListRepositoryInterface;
use Omnyfy\MyReadingList\Api\Data\ReadingListSearchResultsInterfaceFactory;
use Omnyfy\MyReadingList\Api\Data\ReadingListInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Omnyfy\MyReadingList\Model\ResourceModel\ReadingList as ResourceReadingList;
use Omnyfy\MyReadingList\Model\ResourceModel\ReadingList\CollectionFactory as ReadingListCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class ReadingListRepository implements readingListRepositoryInterface
{

    protected $resource;

    protected $readingListFactory;

    protected $readingListCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataReadingListFactory;

    private $storeManager;


    /**
     * @param ResourceReadingList $resource
     * @param ReadingListFactory $readingListFactory
     * @param ReadingListInterfaceFactory $dataReadingListFactory
     * @param ReadingListCollectionFactory $readingListCollectionFactory
     * @param ReadingListSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceReadingList $resource,
        ReadingListFactory $readingListFactory,
        ReadingListInterfaceFactory $dataReadingListFactory,
        ReadingListCollectionFactory $readingListCollectionFactory,
        ReadingListSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->readingListFactory = $readingListFactory;
        $this->readingListCollectionFactory = $readingListCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataReadingListFactory = $dataReadingListFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Omnyfy\MyReadingList\Api\Data\ReadingListInterface $readingList
    ) {
        /* if (empty($readingList->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $readingList->setStoreId($storeId);
        } */
        try {
            $readingList->getResource()->save($readingList);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the readingList: %1',
                $exception->getMessage()
            ));
        }
        return $readingList;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($readingListId)
    {
        $readingList = $this->readingListFactory->create();
        $readingList->getResource()->load($readingList, $readingListId);
        if (!$readingList->getId()) {
            throw new NoSuchEntityException(__('ReadingList with id "%1" does not exist.', $readingListId));
        }
        return $readingList;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->readingListCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
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
        \Omnyfy\MyReadingList\Api\Data\ReadingListInterface $readingList
    ) {
        try {
            $readingList->getResource()->delete($readingList);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the ReadingList: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($readingListId)
    {
        return $this->delete($this->getById($readingListId));
    }
}
