<?php


namespace Omnyfy\Checklist\Model;

use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\CouldNotSaveException;
use Omnyfy\Checklist\Model\ResourceModel\Checklist\CollectionFactory as ChecklistCollectionFactory;
use Omnyfy\Checklist\Api\ChecklistRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Omnyfy\Checklist\Api\Data\ChecklistInterfaceFactory;
use Omnyfy\Checklist\Api\Data\ChecklistSearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\DataObjectHelper;
use Omnyfy\Checklist\Model\ResourceModel\Checklist as ResourceChecklist;

class ChecklistRepository implements checklistRepositoryInterface
{

    protected $resource;

    private $storeManager;

    protected $dataObjectProcessor;

    protected $checklistFactory;

    protected $dataChecklistFactory;

    protected $checklistCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;


    /**
     * @param ResourceChecklist $resource
     * @param ChecklistFactory $checklistFactory
     * @param ChecklistInterfaceFactory $dataChecklistFactory
     * @param ChecklistCollectionFactory $checklistCollectionFactory
     * @param ChecklistSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceChecklist $resource,
        ChecklistFactory $checklistFactory,
        ChecklistInterfaceFactory $dataChecklistFactory,
        ChecklistCollectionFactory $checklistCollectionFactory,
        ChecklistSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->checklistFactory = $checklistFactory;
        $this->checklistCollectionFactory = $checklistCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataChecklistFactory = $dataChecklistFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Omnyfy\Checklist\Api\Data\ChecklistInterface $checklist
    ) {
        /* if (empty($checklist->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $checklist->setStoreId($storeId);
        } */
        try {
            $checklist->getResource()->save($checklist);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the checklist: %1',
                $exception->getMessage()
            ));
        }
        return $checklist;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($checklistId)
    {
        $checklist = $this->checklistFactory->create();
        $checklist->getResource()->load($checklist, $checklistId);
        if (!$checklist->getId()) {
            throw new NoSuchEntityException(__('Checklist with id "%1" does not exist.', $checklistId));
        }
        return $checklist;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->checklistCollectionFactory->create();
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
        \Omnyfy\Checklist\Api\Data\ChecklistInterface $checklist
    ) {
        try {
            $checklist->getResource()->delete($checklist);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Checklist: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($checklistId)
    {
        return $this->delete($this->getById($checklistId));
    }
}
