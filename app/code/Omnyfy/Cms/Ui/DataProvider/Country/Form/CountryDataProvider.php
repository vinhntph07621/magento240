<?php

/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Ui\DataProvider\Country\Form;

use Omnyfy\Cms\Model\ResourceModel\Country\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 */
class CountryDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider {

    /**
     * @var \Omnyfy\Cms\Model\ResourceModel\UserType\Collection
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
     * @param CollectionFactory $countryCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
    $name, $primaryFieldName, $requestFieldName, CollectionFactory $countryCollectionFactory, DataPersistorInterface $dataPersistor, \Magento\Framework\ObjectManagerInterface $objectManager, array $meta = [], array $data = []
    ) {
        $this->collection = $countryCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->objectManager = $objectManager;
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
        /** @var $country \Omnyfy\Cms\Model\UserType */
        foreach ($items as $country) {
            $data = $country->getData();
            //$data['flag_image'][0]['name'] = $data['flag_image'];
            //$data['flag_image'][0]['url'] = $country->getFlatImageUrl();
            
            $map = [
                'flag_image', 'banner_image', 'background_image', 'callout_image',
            ];
            foreach ($map as $key) {
                if (isset($data[$key])) {
                    $name = $data[$key];
                    unset($data[$key]);
                    $data[$key][0] = [
                        'name' => $name,
                        'url' => $country->getCountryImage($key),
                    ];
                }
            }
            $this->loadedData[$country->getId()] = $data;
        }
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($this->loadedData, true)); //die;
        $data = $this->dataPersistor->get('current_model');
        if (!empty($data)) {
            $country = $this->collection->getNewEmptyItem();
            $country->setData($data);
            $this->loadedData[$country->getId()] = $country->getData();
            $this->dataPersistor->clear('current_model');
        } else {
            $country = $this->collection->getNewEmptyItem();
            $data = $this->objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
            $this->loadedData[$country->getId()] = $data;
        }


        return $this->loadedData;
    }

}
