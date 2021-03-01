<?php


namespace Omnyfy\VendorFeatured\Model;

use Omnyfy\VendorFeatured\Api\Data\VendorFeaturedTagInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\CouldNotDeleteException;
use Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeaturedTag as ResourceVendorFeaturedTag;
use Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeaturedTag\CollectionFactory as VendorFeaturedTagCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Omnyfy\VendorFeatured\Api\Data\VendorFeaturedTagSearchResultsInterfaceFactory;
use Omnyfy\VendorFeatured\Api\VendorFeaturedTagRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\DataObjectHelper;

class VendorFeaturedTagRepository implements VendorFeaturedTagRepositoryInterface
{

    protected $vendorFeaturedTagFactory;

    protected $resource;

    protected $dataObjectProcessor;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataVendorFeaturedTagFactory;

    private $storeManager;

    protected $vendorFeaturedTagCollectionFactory;


    /**
     * @param ResourceVendorFeaturedTag $resource
     * @param VendorFeaturedTagFactory $vendorFeaturedTagFactory
     * @param VendorFeaturedTagInterfaceFactory $dataVendorFeaturedTagFactory
     * @param VendorFeaturedTagCollectionFactory $vendorFeaturedTagCollectionFactory
     * @param VendorFeaturedTagSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceVendorFeaturedTag $resource,
        VendorFeaturedTagFactory $vendorFeaturedTagFactory,
        VendorFeaturedTagInterfaceFactory $dataVendorFeaturedTagFactory,
        VendorFeaturedTagCollectionFactory $vendorFeaturedTagCollectionFactory,
        VendorFeaturedTagSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->vendorFeaturedTagFactory = $vendorFeaturedTagFactory;
        $this->vendorFeaturedTagCollectionFactory = $vendorFeaturedTagCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataVendorFeaturedTagFactory = $dataVendorFeaturedTagFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedTagInterface $vendorFeaturedTag
    ) {
        /* if (empty($vendorFeaturedTag->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $vendorFeaturedTag->setStoreId($storeId);
        } */
        try {
            $this->resource->save($vendorFeaturedTag);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the vendorFeaturedTag: %1',
                $exception->getMessage()
            ));
        }
        return $vendorFeaturedTag;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($vendorFeaturedTagId)
    {
        $vendorFeaturedTag = $this->vendorFeaturedTagFactory->create();
        $this->resource->load($vendorFeaturedTag, $vendorFeaturedTagId);
        if (!$vendorFeaturedTag->getId()) {
            throw new NoSuchEntityException(__('vendor_featured_tag with id "%1" does not exist.', $vendorFeaturedTagId));
        }
        return $vendorFeaturedTag;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->vendorFeaturedTagCollectionFactory->create();
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
        \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedTagInterface $vendorFeaturedTag
    ) {
        try {
            $this->resource->delete($vendorFeaturedTag);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the vendor_featured_tag: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($vendorFeaturedTagId)
    {
        return $this->delete($this->getById($vendorFeaturedTagId));
    }
}
