<?php


namespace Omnyfy\Checklist\Model;

use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\CouldNotSaveException;
use Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsSearchResultsInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUserOptions\CollectionFactory as ChecklistItemUserOptionsCollectionFactory;
use Magento\Framework\Api\SortOrder;
use Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsInterfaceFactory;
use Omnyfy\Checklist\Api\ChecklistItemUserOptionsRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUserOptions as ResourceChecklistItemUserOptions;

class ChecklistItemUserOptionsRepository implements checklistItemUserOptionsRepositoryInterface
{

    protected $resource;

    protected $checklistItemUserOptionsFactory;

    private $storeManager;

    protected $checklistItemUserOptionsCollectionFactory;

    protected $dataObjectProcessor;

    protected $dataChecklistItemUserOptionsFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;


    /**
     * @param ResourceChecklistItemUserOptions $resource
     * @param ChecklistItemUserOptionsFactory $checklistItemUserOptionsFactory
     * @param ChecklistItemUserOptionsInterfaceFactory $dataChecklistItemUserOptionsFactory
     * @param ChecklistItemUserOptionsCollectionFactory $checklistItemUserOptionsCollectionFactory
     * @param ChecklistItemUserOptionsSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceChecklistItemUserOptions $resource,
        ChecklistItemUserOptionsFactory $checklistItemUserOptionsFactory,
        ChecklistItemUserOptionsInterfaceFactory $dataChecklistItemUserOptionsFactory,
        ChecklistItemUserOptionsCollectionFactory $checklistItemUserOptionsCollectionFactory,
        ChecklistItemUserOptionsSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->checklistItemUserOptionsFactory = $checklistItemUserOptionsFactory;
        $this->checklistItemUserOptionsCollectionFactory = $checklistItemUserOptionsCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataChecklistItemUserOptionsFactory = $dataChecklistItemUserOptionsFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsInterface $checklistItemUserOptions
    ) {
        /* if (empty($checklistItemUserOptions->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $checklistItemUserOptions->setStoreId($storeId);
        } */
        try {
            $checklistItemUserOptions->getResource()->save($checklistItemUserOptions);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the checklist item options: %1',
                $exception->getMessage()
            ));
        }
        return $checklistItemUserOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($checklistItemUserOptionsId)
    {
        $checklistItemUserOptions = $this->checklistItemUserOptionsFactory->create();
        $checklistItemUserOptions->getResource()->load($checklistItemUserOptions, $checklistItemUserOptionsId);
        if (!$checklistItemUserOptions->getId()) {
            throw new NoSuchEntityException(__('ChecklistItemUserOptions with id "%1" does not exist.', $checklistItemUserOptionsId));
        }
        return $checklistItemUserOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->checklistItemUserOptionsCollectionFactory->create();
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
        \Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsInterface $checklistItemUserOptions
    ) {
        try {
            $checklistItemUserOptions->getResource()->delete($checklistItemUserOptions);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the ChecklistItemUserOptions: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($checklistItemUserOptionsId)
    {
        return $this->delete($this->getById($checklistItemUserOptionsId));
    }
}
