<?php


namespace Omnyfy\MyReadingList\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Omnyfy\MyReadingList\Model\ResourceModel\ReadingListArticles as ResourceReadingListArticles;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Omnyfy\MyReadingList\Api\Data\ReadingListArticlesSearchResultsInterfaceFactory;
use Omnyfy\MyReadingList\Api\Data\ReadingListArticlesInterfaceFactory;
use Omnyfy\MyReadingList\Model\ResourceModel\ReadingListArticles\CollectionFactory as ReadingListArticlesCollectionFactory;
use Magento\Framework\Reflection\DataObjectProcessor;
use Omnyfy\MyReadingList\Api\ReadingListArticlesRepositoryInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;

class ReadingListArticlesRepository implements ReadingListArticlesRepositoryInterface
{

    private $storeManager;
    protected $dataReadingListArticlesFactory;

    protected $resource;

    protected $searchResultsFactory;

    protected $readingListArticlesFactory;

    protected $readingListArticlesCollectionFactory;

    protected $dataObjectProcessor;

    protected $dataObjectHelper;


    /**
     * @param ResourceReadingListArticles $resource
     * @param ReadingListArticlesFactory $readingListArticlesFactory
     * @param ReadingListArticlesInterfaceFactory $dataReadingListArticlesFactory
     * @param ReadingListArticlesCollectionFactory $readingListArticlesCollectionFactory
     * @param ReadingListArticlesSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceReadingListArticles $resource,
        ReadingListArticlesFactory $readingListArticlesFactory,
        ReadingListArticlesInterfaceFactory $dataReadingListArticlesFactory,
        ReadingListArticlesCollectionFactory $readingListArticlesCollectionFactory,
        ReadingListArticlesSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->readingListArticlesFactory = $readingListArticlesFactory;
        $this->readingListArticlesCollectionFactory = $readingListArticlesCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataReadingListArticlesFactory = $dataReadingListArticlesFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Omnyfy\MyReadingList\Api\Data\ReadingListArticlesInterface $readingListArticles
    ) {
        /* if (empty($readingListArticles->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $readingListArticles->setStoreId($storeId);
        } */
        try {
            $this->resource->save($readingListArticles);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the readingListArticles: %1',
                $exception->getMessage()
            ));
        }
        return $readingListArticles;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($readingListArticlesId)
    {
        $readingListArticles = $this->readingListArticlesFactory->create();
        $this->resource->load($readingListArticles, $readingListArticlesId);
        if (!$readingListArticles->getId()) {
            throw new NoSuchEntityException(__('ReadingListArticles with id "%1" does not exist. %2', $readingListArticlesId,$readingListArticles->getId() ));
        }
        return $readingListArticles;
    }

    /**getReadingListArticleId
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->readingListArticlesCollectionFactory->create();
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
        \Omnyfy\MyReadingList\Api\Data\ReadingListArticlesInterface $readingListArticles
    ) {
        try {
            $this->resource->delete($readingListArticles);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the ReadingListArticles: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($readingListArticlesId)
    {
        return $this->delete($this->getById($readingListArticlesId));
    }
}