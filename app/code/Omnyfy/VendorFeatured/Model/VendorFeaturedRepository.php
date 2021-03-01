<?php


namespace Omnyfy\VendorFeatured\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Omnyfy\VendorFeatured\Api\VendorFeaturedRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Store\Model\StoreManagerInterface;
use Omnyfy\VendorFeatured\Api\Data\VendorFeaturedInterfaceFactory;
use Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeatured\CollectionFactory as VendorFeaturedCollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\SortOrder;
use Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeatured as ResourceVendorFeatured;
use Omnyfy\VendorFeatured\Api\Data\VendorFeaturedSearchResultsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class VendorFeaturedRepository implements VendorFeaturedRepositoryInterface
{

    protected $resource;

    protected $dataObjectProcessor;

    protected $vendorFeaturedFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataVendorFeaturedFactory;

    private $storeManager;

    protected $vendorFeaturedCollectionFactory;


    /**
     * @param ResourceVendorFeatured $resource
     * @param VendorFeaturedFactory $vendorFeaturedFactory
     * @param VendorFeaturedInterfaceFactory $dataVendorFeaturedFactory
     * @param VendorFeaturedCollectionFactory $vendorFeaturedCollectionFactory
     * @param VendorFeaturedSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceVendorFeatured $resource,
        VendorFeaturedFactory $vendorFeaturedFactory,
        VendorFeaturedInterfaceFactory $dataVendorFeaturedFactory,
        VendorFeaturedCollectionFactory $vendorFeaturedCollectionFactory,
        VendorFeaturedSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->vendorFeaturedFactory = $vendorFeaturedFactory;
        $this->vendorFeaturedCollectionFactory = $vendorFeaturedCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataVendorFeaturedFactory = $dataVendorFeaturedFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedInterface $vendorFeatured
    ) {
        /* if (empty($vendorFeatured->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $vendorFeatured->setStoreId($storeId);
        } */
        try {
            $this->resource->save($vendorFeatured);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the vendorFeatured: %1',
                $exception->getMessage()
            ));
        }
        return $vendorFeatured;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($vendorFeaturedId)
    {
        $vendorFeatured = $this->vendorFeaturedFactory->create();
        $this->resource->load($vendorFeatured, $vendorFeaturedId);
        if (!$vendorFeatured->getId()) {
            throw new NoSuchEntityException(__('vendor_featured with id "%1" does not exist.', $vendorFeaturedId));
        }
        return $vendorFeatured;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->vendorFeaturedCollectionFactory->create();
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
        \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedInterface $vendorFeatured
    ) {
        try {
            $this->resource->delete($vendorFeatured);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the vendor_featured: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($vendorFeaturedId)
    {
        return $this->delete($this->getById($vendorFeaturedId));
    }
}
