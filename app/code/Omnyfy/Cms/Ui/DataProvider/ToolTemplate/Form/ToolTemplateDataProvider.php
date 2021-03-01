<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */
namespace Omnyfy\Cms\Ui\DataProvider\ToolTemplate\Form;

use Omnyfy\Cms\Model\ResourceModel\ToolTemplate\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 */
class ToolTemplateDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
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
     * @param CollectionFactory $userTypeCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $userTypeCollectionFactory,
        DataPersistorInterface $dataPersistor,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $userTypeCollectionFactory->create();
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
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var $toolTemplate \Omnyfy\Cms\Model\ToolTemplate */
		
        foreach ($items as $tools) {
            $data = $tools->getData();
            
            $map = [
                'icon', 'upload_template',
            ];
            foreach ($map as $key) {
                if (isset($data[$key])) {
                    $name = $data[$key];
                    unset($data[$key]);
                    $data[$key][0] = [
                        'name' => $name,
                        'url' => $tools->getImage($key),
                    ];
                }
            }
            $this->loadedData[$tools->getId()] = $data;
        }
		
		$data = $this->dataPersistor->get('current_model');
        if (!empty($data)) {
            $tools = $this->collection->getNewEmptyItem();
            $tools->setData($data);
            $this->loadedData[$tools->getId()] = $tools->getData();
            $this->dataPersistor->clear('current_model');
        } else {
            $tools = $this->collection->getNewEmptyItem();
            $data = $this->objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
            $this->loadedData[$tools->getId()] = $data;
        }
		
        /* foreach ($items as $toolTemplate) {
            $this->loadedData[$toolTemplate->getId()] = $toolTemplate->getData();
        }

        $data = $this->dataPersistor->get('current_model');
        if (!empty($data)) {
            $toolTemplate = $this->collection->getNewEmptyItem();
            $toolTemplate->setData($data);
            $this->loadedData[$userType->getId()] = $toolTemplate->getData();
            $this->dataPersistor->clear('current_model');
        }
 */
        return $this->loadedData;
    }
}
