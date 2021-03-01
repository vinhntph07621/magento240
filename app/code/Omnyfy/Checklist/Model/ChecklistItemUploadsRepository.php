<?php


namespace Omnyfy\Checklist\Model;

use Omnyfy\Checklist\Api\ChecklistItemUploadsRepositoryInterface;
use Omnyfy\Checklist\Api\Data\ChecklistItemUploadsSearchResultsInterfaceFactory;
use Omnyfy\Checklist\Api\Data\ChecklistItemUploadsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUploads as ResourceChecklistItemUploads;
use Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUploads\CollectionFactory as ChecklistItemUploadsCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class ChecklistItemUploadsRepository implements checklistItemUploadsRepositoryInterface
{

    protected $resource;

    protected $checklistItemUploadsFactory;

    protected $checklistItemUploadsCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataChecklistItemUploadsFactory;

    private $storeManager;


    /**
     * @param ResourceChecklistItemUploads $resource
     * @param ChecklistItemUploadsFactory $checklistItemUploadsFactory
     * @param ChecklistItemUploadsInterfaceFactory $dataChecklistItemUploadsFactory
     * @param ChecklistItemUploadsCollectionFactory $checklistItemUploadsCollectionFactory
     * @param ChecklistItemUploadsSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceChecklistItemUploads $resource,
        ChecklistItemUploadsFactory $checklistItemUploadsFactory,
        ChecklistItemUploadsInterfaceFactory $dataChecklistItemUploadsFactory,
        ChecklistItemUploadsCollectionFactory $checklistItemUploadsCollectionFactory,
        ChecklistItemUploadsSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->checklistItemUploadsFactory = $checklistItemUploadsFactory;
        $this->checklistItemUploadsCollectionFactory = $checklistItemUploadsCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataChecklistItemUploadsFactory = $dataChecklistItemUploadsFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Omnyfy\Checklist\Api\Data\ChecklistItemUploadsInterface $checklistItemUploads
    ) {
        /* if (empty($checklistItemUploads->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $checklistItemUploads->setStoreId($storeId);
        } */
        try {
            $checklistItemUploads->getResource()->save($checklistItemUploads);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the checklistItemUploads: %1',
                $exception->getMessage()
            ));
        }
        return $checklistItemUploads;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($checklistItemUploadsId)
    {
        $checklistItemUploads = $this->checklistItemUploadsFactory->create();
        $checklistItemUploads->getResource()->load($checklistItemUploads, $checklistItemUploadsId);
        if (!$checklistItemUploads->getId()) {
            throw new NoSuchEntityException(__('ChecklistItemUploads with id "%1" does not exist.', $checklistItemUploadsId));
        }
        return $checklistItemUploads;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->checklistItemUploadsCollectionFactory->create();
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
        \Omnyfy\Checklist\Api\Data\ChecklistItemUploadsInterface $checklistItemUploads
    ) {
        try {
            $checklistItemUploads->getResource()->delete($checklistItemUploads);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the ChecklistItemUploads: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($checklistItemUploadsId)
    {
        return $this->delete($this->getById($checklistItemUploadsId));
    }
}
