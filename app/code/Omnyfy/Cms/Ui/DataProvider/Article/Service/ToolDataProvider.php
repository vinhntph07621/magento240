<?php

namespace Omnyfy\Cms\Ui\DataProvider\Article\Service;

use Omnyfy\Cms\Model\ResourceModel\ToolTemplate\Collection;
use Omnyfy\Cms\Model\ResourceModel\ToolTemplate\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Omnyfy\Cms\Model\Config\Source\ToolTemplate;
/**
 * Class ArticleDataProvider
 */
class ToolDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider {

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
    $name, $primaryFieldName, $requestFieldName, CollectionFactory $collectionFactory, RequestInterface $request, \Magento\Framework\Api\Search\ReportingInterface $reporting, \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder, \Magento\Framework\Api\FilterBuilder $filterBuilder, ToolTemplate $toolTemplateOption, array $meta = [], array $data = []
    ) {
        parent::__construct(
                $name, $primaryFieldName, $requestFieldName, $reporting, $searchCriteriaBuilder, $request, $filterBuilder, $meta, $data
        );
		$this->_toolTemplateOption = $toolTemplateOption;
        $this->collection = $collectionFactory->create();
        $this->request = $request;
    }

    /**
     * @param SearchResultInterface $searchResult
     * @return array
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult) {
        $arrItems = [];
        $arrItems['totalRecords'] = $searchResult->getTotalCount();

        $arrItems['items'] = [];
        foreach ($searchResult->getItems() as $item) {
            $arrItems['items'][] = $item->getData();
        }

        return $arrItems;
    }

    /**
     * {@inheritdoc}
     */
    public function getData() {
        /** @var Collection $collection */
        $collection = $this->getSearchResult();

        $collection
                ->addFieldToSelect([
                    'id',
                    'title',
                    'type',
        ]);
        $collection->addFieldToFilter('status', 1);
		
		foreach ($collection as $tool) {
            $tool->setData('type', $this->getTypeValue($tool['type']));
        }
		
        $data = $this->searchResultToOutput($collection);
        return $data;
    }

   public function getTypeValue($field) {
        $fieldLabels = $this->_toolTemplateOption->toArray();
        return $fieldLabels[$field];
    }

    /**
     * @return AbstractCollection
     */
    public function getCollection() {
        return $this->collection;
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
