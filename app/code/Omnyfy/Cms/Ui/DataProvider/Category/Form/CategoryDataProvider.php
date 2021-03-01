<?php

/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Ui\DataProvider\Category\Form;

use Omnyfy\Cms\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 */
class CategoryDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider {

    /**
     * @var \Omnyfy\Cms\Model\ResourceModel\Category\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $categoryCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
    $name, $primaryFieldName, $requestFieldName, CollectionFactory $categoryCollectionFactory, DataPersistorInterface $dataPersistor, array $meta = [], array $data = []
    ) {
        $this->collection = $categoryCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->meta = $this->prepareMeta($this->meta);
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta) {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData() {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var $category \Omnyfy\Cms\Model\Category */
        foreach ($items as $category) {
            $data = $category->getData();
            /* Prepare Category Icon */
            $map = [
                'category_icon' => 'getCategoryIcon',
                'category_banner' => 'getCategoryBanner',
            ];
            foreach ($map as $key => $method) {
                if (isset($data[$key])) {
                    $name = $data[$key];
                    unset($data[$key]);
                    $data[$key][0] = [
                        'name' => $name,
                        'url' => $category->$method(),
                    ];
                }
            }
            
            /* Prepare related articles */
            $collection = $category->getCategoryArticles();
            $items = [];
            foreach($collection as $item) {
                $items[] = [
                    'id' => $item->getId(),
                    'title' => $item->getTitle(),
                    'position' => $item->getPosition()
                ];
            }
            $data['data']['links']['article'] = $items;
            
            $this->loadedData[$category->getId()] = $data;
        }

        $data = $this->dataPersistor->get('current_model');
        if (!empty($data)) {
            $category = $this->collection->getNewEmptyItem();
            $category->setData($data);
            $this->loadedData[$category->getId()] = $category->getData();
            $this->dataPersistor->clear('current_model');
            }
        //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug('category_data '.print_r($this->loadedData, true));
        return $this->loadedData;
    }

}
