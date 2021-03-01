<?php


namespace Omnyfy\Checklist\Model;

use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Store\Model\StoreManagerInterface;
use Omnyfy\Checklist\Api\Data\ChecklistItemsInterfaceFactory;
use Omnyfy\Checklist\Model\ResourceModel\ChecklistItems as ResourceChecklistItems;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SortOrder;
use Omnyfy\Checklist\Api\Data\ChecklistItemsSearchResultsInterfaceFactory;
use Omnyfy\Checklist\Api\ChecklistItemsRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Omnyfy\Checklist\Model\ResourceModel\ChecklistItems\CollectionFactory as ChecklistItemsCollectionFactory;

class ChecklistItemsRepository implements checklistItemsRepositoryInterface
{

    protected $resource;

    protected $checklistItemsFactory;

    protected $dataChecklistItemsFactory;

    private $storeManager;

    protected $dataObjectProcessor;

    protected $checklistItemsCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;


    /**
     * @param ResourceChecklistItems $resource
     * @param ChecklistItemsFactory $checklistItemsFactory
     * @param ChecklistItemsInterfaceFactory $dataChecklistItemsFactory
     * @param ChecklistItemsCollectionFactory $checklistItemsCollectionFactory
     * @param ChecklistItemsSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceChecklistItems $resource,
        ChecklistItemsFactory $checklistItemsFactory,
        ChecklistItemsInterfaceFactory $dataChecklistItemsFactory,
        ChecklistItemsCollectionFactory $checklistItemsCollectionFactory,
        ChecklistItemsSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->checklistItemsFactory = $checklistItemsFactory;
        $this->checklistItemsCollectionFactory = $checklistItemsCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataChecklistItemsFactory = $dataChecklistItemsFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Omnyfy\Checklist\Api\Data\ChecklistItemsInterface $checklistItems
    ) {
        /* if (empty($checklistItems->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $checklistItems->setStoreId($storeId);
        } */
        try {
            $checklistItems->getResource()->save($checklistItems);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the checklistItems: %1',
                $exception->getMessage()
            ));
        }
        return $checklistItems;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($checklistItemsId)
    {
        $checklistItems = $this->checklistItemsFactory->create();
        $checklistItems->getResource()->load($checklistItems, $checklistItemsId);
        if (!$checklistItems->getId()) {
            throw new NoSuchEntityException(__('ChecklistItems with id "%1" does not exist.', $checklistItemsId));
        }
        return $checklistItems;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->checklistItemsCollectionFactory->create();
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
        \Omnyfy\Checklist\Api\Data\ChecklistItemsInterface $checklistItems
    ) {
        try {
            $checklistItems->getResource()->delete($checklistItems);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the ChecklistItems: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($checklistItemsId)
    {
        return $this->delete($this->getById($checklistItemsId));
    }
}
