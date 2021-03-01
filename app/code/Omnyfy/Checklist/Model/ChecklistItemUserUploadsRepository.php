<?php


namespace Omnyfy\Checklist\Model;

use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Store\Model\StoreManagerInterface;
use Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SortOrder;
use Omnyfy\Checklist\Api\ChecklistItemUserUploadsRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUserUploads\CollectionFactory as ChecklistItemUserUploadsCollectionFactory;
use Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsSearchResultsInterfaceFactory;
use Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUserUploads as ResourceChecklistItemUserUploads;

class ChecklistItemUserUploadsRepository implements checklistItemUserUploadsRepositoryInterface
{

    protected $checklistItemUserUploadsFactory;

    protected $resource;

    protected $dataChecklistItemUserUploadsFactory;

    private $storeManager;

    protected $dataObjectProcessor;

    protected $checklistItemUserUploadsCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;


    /**
     * @param ResourceChecklistItemUserUploads $resource
     * @param ChecklistItemUserUploadsFactory $checklistItemUserUploadsFactory
     * @param ChecklistItemUserUploadsInterfaceFactory $dataChecklistItemUserUploadsFactory
     * @param ChecklistItemUserUploadsCollectionFactory $checklistItemUserUploadsCollectionFactory
     * @param ChecklistItemUserUploadsSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceChecklistItemUserUploads $resource,
        ChecklistItemUserUploadsFactory $checklistItemUserUploadsFactory,
        ChecklistItemUserUploadsInterfaceFactory $dataChecklistItemUserUploadsFactory,
        ChecklistItemUserUploadsCollectionFactory $checklistItemUserUploadsCollectionFactory,
        ChecklistItemUserUploadsSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->checklistItemUserUploadsFactory = $checklistItemUserUploadsFactory;
        $this->checklistItemUserUploadsCollectionFactory = $checklistItemUserUploadsCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataChecklistItemUserUploadsFactory = $dataChecklistItemUserUploadsFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsInterface $checklistItemUserUploads
    ) {
        /* if (empty($checklistItemUserUploads->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $checklistItemUserUploads->setStoreId($storeId);
        } */
        try {
            $checklistItemUserUploads->getResource()->save($checklistItemUserUploads);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the checklistItemUserUploads: %1',
                $exception->getMessage()
            ));
        }
        return $checklistItemUserUploads;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($checklistItemUserUploadsId)
    {
        $checklistItemUserUploads = $this->checklistItemUserUploadsFactory->create();
        $checklistItemUserUploads->getResource()->load($checklistItemUserUploads, $checklistItemUserUploadsId);
        if (!$checklistItemUserUploads->getId()) {
            throw new NoSuchEntityException(__('ChecklistItemUserUploads with id "%1" does not exist.', $checklistItemUserUploadsId));
        }
        return $checklistItemUserUploads;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->checklistItemUserUploadsCollectionFactory->create();
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
        \Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsInterface $checklistItemUserUploads
    ) {
        try {
            $checklistItemUserUploads->getResource()->delete($checklistItemUserUploads);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the ChecklistItemUserUploads: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($checklistItemUserUploadsId)
    {
        return $this->delete($this->getById($checklistItemUserUploadsId));
    }
}
