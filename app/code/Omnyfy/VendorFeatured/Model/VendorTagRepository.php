<?php


namespace Omnyfy\VendorFeatured\Model;

use Omnyfy\VendorFeatured\Api\VendorTagRepositoryInterface;
use Omnyfy\VendorFeatured\Api\Data\VendorTagSearchResultsInterfaceFactory;
use Omnyfy\VendorFeatured\Api\Data\VendorTagInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Omnyfy\VendorFeatured\Model\ResourceModel\VendorTag as ResourceVendorTag;
use Omnyfy\VendorFeatured\Model\ResourceModel\VendorTag\CollectionFactory as VendorTagCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class VendorTagRepository implements VendorTagRepositoryInterface
{

    protected $resource;

    protected $vendorTagFactory;

    protected $vendorTagCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataVendorTagFactory;

    private $storeManager;


    /**
     * @param ResourceVendorTag $resource
     * @param VendorTagFactory $vendorTagFactory
     * @param VendorTagInterfaceFactory $dataVendorTagFactory
     * @param VendorTagCollectionFactory $vendorTagCollectionFactory
     * @param VendorTagSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceVendorTag $resource,
        VendorTagFactory $vendorTagFactory,
        VendorTagInterfaceFactory $dataVendorTagFactory,
        VendorTagCollectionFactory $vendorTagCollectionFactory,
        VendorTagSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->vendorTagFactory = $vendorTagFactory;
        $this->vendorTagCollectionFactory = $vendorTagCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataVendorTagFactory = $dataVendorTagFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Omnyfy\VendorFeatured\Api\Data\VendorTagInterface $vendorTag
    ) {
        /* if (empty($vendorTag->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $vendorTag->setStoreId($storeId);
        } */
        try {
            $this->resource->save($vendorTag);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the vendorTag: %1',
                $exception->getMessage()
            ));
        }
        return $vendorTag;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($vendorTagId)
    {
        $vendorTag = $this->vendorTagFactory->create();
        $this->resource->load($vendorTag, $vendorTagId);
        if (!$vendorTag->getId()) {
            throw new NoSuchEntityException(__('vendor_tag with id "%1" does not exist.', $vendorTagId));
        }
        return $vendorTag;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->vendorTagCollectionFactory->create();
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
        \Omnyfy\VendorFeatured\Api\Data\VendorTagInterface $vendorTag
    ) {
        try {
            $this->resource->delete($vendorTag);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the vendor_tag: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($vendorTagId)
    {
        return $this->delete($this->getById($vendorTagId));
    }
}
