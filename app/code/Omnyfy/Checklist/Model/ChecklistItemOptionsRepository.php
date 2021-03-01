<?php


namespace Omnyfy\Checklist\Model;

use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SortOrder;
use Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterfaceFactory;
use Omnyfy\Checklist\Model\ResourceModel\ChecklistItemOptions\CollectionFactory as ChecklistItemOptionsCollectionFactory;
use Omnyfy\Checklist\Api\ChecklistItemOptionsRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Omnyfy\Checklist\Model\ResourceModel\ChecklistItemOptions as ResourceChecklistItemOptions;
use Omnyfy\Checklist\Api\Data\ChecklistItemOptionsSearchResultsInterfaceFactory;

class ChecklistItemOptionsRepository implements checklistItemOptionsRepositoryInterface
{

    protected $resource;

    private $storeManager;

    protected $dataObjectProcessor;

    protected $dataChecklistItemOptionsFactory;

    protected $checklistItemOptionsFactory;

    protected $checklistItemOptionsCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;


    /**
     * @param ResourceChecklistItemOptions $resource
     * @param ChecklistItemOptionsFactory $checklistItemOptionsFactory
     * @param ChecklistItemOptionsInterfaceFactory $dataChecklistItemOptionsFactory
     * @param ChecklistItemOptionsCollectionFactory $checklistItemOptionsCollectionFactory
     * @param ChecklistItemOptionsSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceChecklistItemOptions $resource,
        ChecklistItemOptionsFactory $checklistItemOptionsFactory,
        ChecklistItemOptionsInterfaceFactory $dataChecklistItemOptionsFactory,
        ChecklistItemOptionsCollectionFactory $checklistItemOptionsCollectionFactory,
        ChecklistItemOptionsSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->checklistItemOptionsFactory = $checklistItemOptionsFactory;
        $this->checklistItemOptionsCollectionFactory = $checklistItemOptionsCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataChecklistItemOptionsFactory = $dataChecklistItemOptionsFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface $checklistItemOptions
    ) {
        /* if (empty($checklistItemOptions->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $checklistItemOptions->setStoreId($storeId);
        } */
        try {
            $checklistItemOptions->getResource()->save($checklistItemOptions);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the checklistItemOptions: %1',
                $exception->getMessage()
            ));
        }
        return $checklistItemOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($checklistItemOptionsId)
    {
        $checklistItemOptions = $this->checklistItemOptionsFactory->create();
        $checklistItemOptions->getResource()->load($checklistItemOptions, $checklistItemOptionsId);
        if (!$checklistItemOptions->getId()) {
            throw new NoSuchEntityException(__('ChecklistItemOptions with id "%1" does not exist.', $checklistItemOptionsId));
        }
        return $checklistItemOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->checklistItemOptionsCollectionFactory->create();
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
        \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface $checklistItemOptions
    ) {
        try {
            $checklistItemOptions->getResource()->delete($checklistItemOptions);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the ChecklistItemOptions: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($checklistItemOptionsId)
    {
        return $this->delete($this->getById($checklistItemOptionsId));
    }
}
