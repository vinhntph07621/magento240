<?php

namespace Omnyfy\Cms\Ui\DataProvider\Article\Service;

use Omnyfy\Vendor\Model\Resource\Location\Collection;
use Omnyfy\Vendor\Model\Resource\Location\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Omnyfy\Vendor\Ui\Component\LocationGridDataProvider as DataProvider;

/**
 * Class ArticleDataProvider
 */
class ServiceDataProvider extends DataProvider {

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var article
     */
    private $article;

    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
    $name, $primaryFieldName, $requestFieldName, CollectionFactory $collectionFactory, RequestInterface $request, \Magento\Framework\Api\Search\ReportingInterface $reporting, \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder, \Magento\Framework\Api\FilterBuilder $filterBuilder, array $meta = [], array $data = []
    ) {
        parent::__construct(
                $name, $primaryFieldName, $requestFieldName, $reporting, $searchCriteriaBuilder, $request, $filterBuilder, $meta, $data
        );
        $this->collection = $collectionFactory->create();
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getData() {
        /** @var Collection $collection */
        $collection = $this->getSearchResult();

        $collection
                ->addFieldToSelect([
                    'entity_id',
                    'vendor_id',
                    'location_name',
        ]);
        $collection->addFieldToFilter('status', 1);

        $data = $this->searchResultToOutput($collection);
        return $data;
    }

    /**
     * @return AbstractCollection
     */
    public function getCollection() {
        return $this->collection;
    }

    /**
     * Add specific filters
     *
     * @param Collection $collection
     * @return Collection
     */
    protected function addCollectionFilters(Collection $collection) {
        return $collection;
    }

    /**
     * Retrieve article
     *
     * @return ArticleInterface|null
     */
    protected function getLocation() {
        if (null !== $this->article) {
            return $this->article;
        }

        if (!($id = $this->request->getParam('current_article_id'))) {
            return null;
        }

        return $this->article = $this->locationRepository->getById($id);
    }

    /**
     * Add field to select
     *
     * @param string|array $field
     * @param string|null $alias
     * @return void
     */
    public function addField($field, $alias = null) {
        $this->getCollection()->addFieldToSelect($field, $alias);
    }

}
