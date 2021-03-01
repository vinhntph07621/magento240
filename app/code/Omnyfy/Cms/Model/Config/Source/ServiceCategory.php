<?php

namespace Omnyfy\Cms\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\Category as CategoryModel;

class ServiceCategory extends \Magento\Catalog\Ui\Component\Product\Form\Categories\Options {

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var RequestInterface
     */
    protected $request;
    protected $helperAdmin;

    /**
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param RequestInterface $request
     */
    public function __construct(
    CategoryCollectionFactory $categoryCollectionFactory, RequestInterface $request, \Omnyfy\Cms\Helper\Admin $helperAdmin
    ) {
        parent::__construct($categoryCollectionFactory, $request);
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->request = $request;
        $this->helperAdmin = $helperAdmin;
    }

    /**
     * Retrieve categories tree
     *
     * @return array
     */
    protected function getCategoriesTree() {

        if ($this->categoriesTree === null) {
            $storeId = $this->request->getParam('store');
            /* @var $matchingNamesCollection \Magento\Catalog\Model\ResourceModel\Category\Collection */
            $matchingNamesCollection = $this->categoryCollectionFactory->create();

            $matchingNamesCollection->addAttributeToSelect('path')
                    ->addAttributeToFilter('entity_id', ['neq' => CategoryModel::TREE_ROOT_ID])
                    ->setStoreId($storeId);

            $shownCategoriesIds = [];

            /** @var \Magento\Catalog\Model\Category $category */
            foreach ($matchingNamesCollection as $category) {
                foreach (explode('/', $category->getPath()) as $parentId) {
                    //if($parentId == 3)
                    $shownCategoriesIds[$parentId] = 1;
                }
            }

            /* @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection */
            $collection = $this->categoryCollectionFactory->create();

            $collection->addAttributeToFilter('entity_id', ['in' => array_keys($shownCategoriesIds)])
                    ->addAttributeToSelect(['name', 'is_active', 'parent_id'])
                    ->setStoreId($storeId);

            $categoryById = [
                CategoryModel::TREE_ROOT_ID => [
                    'value' => CategoryModel::TREE_ROOT_ID
                ],
            ];

            foreach ($collection as $category) {
                foreach ([$category->getId(), $category->getParentId()] as $categoryId) {
                    if (!isset($categoryById[$categoryId])) {
                        $categoryById[$categoryId] = ['value' => $categoryId];
                    }
                }
                // Code for show only service categories 
                $serviceCategoryId = $this->helperAdmin->getGeneralConfig('service_category_id', $this->helperAdmin->getStoreId());
                if (isset($serviceCategoryId) && $serviceCategoryId != '' && ($category->getParentId() == $serviceCategoryId || $category->getId() == $serviceCategoryId)) {
                    $categoryById[$category->getId()]['is_active'] = $category->getIsActive();
                    $categoryById[$category->getId()]['label'] = $category->getName();
                    $categoryById[$category->getParentId()]['optgroup'][] = &$categoryById[$category->getId()];
                }
            }
            if (isset($serviceCategoryId) && $serviceCategoryId != '') {
                $this->categoriesTree = $categoryById[CategoryModel::TREE_ROOT_ID]['optgroup'];
            }
        }
        if (!empty($this->categoriesTree)) {
            return $this->categoriesTree;
        } else{
            return [];
        }
            
    }

}
