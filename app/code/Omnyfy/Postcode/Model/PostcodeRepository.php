<?php

namespace Omnyfy\Postcode\Model;

use \Omnyfy\Postcode\Api\PostcodeRepositoryInterface;

class PostcodeRepository implements PostcodeRepositoryInterface
{

    /**
     * @var array
     */
    protected $_instances = [];

    /**
     * @var \Omnyfy\Postcode\Model\PostcodeFactory
     */
    protected $postcodeFactory;

    /**
     * @var \Omnyfy\Postcode\Model\ResourceModel\Postcode\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Omnyfy\Postcode\Api\Data\PostcodeSearchResultInterfaceFactory
     */
    protected $_searchResultFactory;

    /**
     * @var \Omnyfy\Postcode\Api\Data\PostcodeSimpleParameterSearchInterfaceFactory
     */
    protected $_simpleParamSearchFactory;

    /**
     * Construct
     *
     * @param \Omnyfy\Postcode\Model\PostcodeFactory $postcodeFactory
     * @param \Omnyfy\Postcode\Model\ResourceModel\Postcode\CollectionFactory $collectionFactory
     * @param \Omnyfy\Postcode\Api\Data\PostcodeSearchResultInterfaceFactory $searchResultFactory
     * @param \Omnyfy\Postcode\Api\Data\PostcodeSimpleParameterSearchInterfaceFactory $simpleParamSearchFactory
     */
    public function __construct(
        \Omnyfy\Postcode\Model\PostcodeFactory $postcodeFactory,
        \Omnyfy\Postcode\Model\ResourceModel\Postcode\CollectionFactory $collectionFactory,
        \Omnyfy\Postcode\Api\Data\PostcodeSearchResultInterfaceFactory $searchResultFactory,
        \Omnyfy\Postcode\Api\Data\PostcodeSimpleParameterSearchInterfaceFactory $simpleParamSearchFactory
    ) {
        $this->_postcodeFactory = $postcodeFactory;
        $this->_collectionFactory = $collectionFactory;
        $this->_searchResultFactory = $searchResultFactory;
        $this->_simpleParamSearchFactory = $simpleParamSearchFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($postcodeId, $forceReload = false)
    {
        if (!isset($this->_instances[$postcodeId]) || $forceReload) {
            $postcode = $this->_postcodeFactory->create();
            /* @var $postcode \Omnyfy\Postcode\Model\Postcode */

            $postcode->load($postcodeId);
            if (!$postcode->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('Requested postcode doesn\'t exist'));
            }

            $this->_instances[$postcodeId] = $postcode;
        }

        return $this->_instances[$postcodeId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->_collectionFactory->create();
        /* @var $collection \Omnyfy\Postcode\Model\ResourceModel\Postcode\Collection */

        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $fields = [];
            foreach ($group->getFilters() as $filter) {
                $conditionType = $filter->getConditionType() ? $filter->getConditionType() : 'eq';

                $fields[] = ['attribute' => $filter->getField(), $conditionType => $filter->getValue()];
            }

            if ($fields) {
                $collection->addFieldToFilter($fields);
            }
        }

        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            /* @var $sortOrder \Magento\Framework\Api\Sortorder */
            $field = $sortOrder->getField();
            $collection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }

        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->load();

        $searchResult = $this->_searchResultFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * {@inheritdoc}
     */
    public function getListByKeyword($keyword)
    {
        $collection = $this->_collectionFactory->create();
        /* @var $collection \Omnyfy\Postcode\Model\ResourceModel\Postcode\Collection */

        $collection->addKeywordFilter($keyword);
        $collection->setPageSize(10);

        $searchResult = $this->_simpleParamSearchFactory->create();
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * {@inheritdoc}
     */
    public function getClosest($lon, $lat)
    {
        $collection = $this->_collectionFactory->create();
        //Please be ware of the sequence of arguments here. Latitude and Longitude
        $collection->filterDistance($lat, $lon);

        $result = $collection->getFirstItem();
        if (empty($result)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException();
        }
        return $result;
    }

}
