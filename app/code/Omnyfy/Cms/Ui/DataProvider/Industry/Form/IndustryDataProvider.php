<?php
/**
 * Project: CMS Industry M2.
 * User: abhay
 * Date: 01/05/17
 * Time: 2:30 PM
 */

namespace Omnyfy\Cms\Ui\DataProvider\Industry\Form;

use Omnyfy\Cms\Model\ResourceModel\Industry\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 */
class IndustryDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider {

    /**
     * @var \Omnyfy\Cms\Model\ResourceModel\Industry\Collection
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
     * @param CollectionFactory $industryCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
    $name, $primaryFieldName, $requestFieldName, CollectionFactory $industryCollectionFactory, DataPersistorInterface $dataPersistor, \Magento\Framework\ObjectManagerInterface $objectManager, array $meta = [], array $data = []
    ) {
        $this->collection = $industryCollectionFactory->create();
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
        /** @var $industry \Omnyfy\Cms\Model\Industry */
        foreach ($items as $industry) {
            $data = $industry->getData();
            //$data['flag_image'][0]['name'] = $data['flag_image'];
            //$data['flag_image'][0]['url'] = $industry->getFlatImageUrl();
            
            $map = [
                'background_image', 'industry_profile_image',
            ];
            foreach ($map as $key) {
                if (isset($data[$key])) {
                    $name = $data[$key];
                    unset($data[$key]);
                    $data[$key][0] = [
                        'name' => $name,
                        'url' => $industry->getIndustryImage($key),
                    ];
                }
            }
            $this->loadedData[$industry->getId()] = $data;
        }
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($this->loadedData, true)); //die;
        $data = $this->dataPersistor->get('current_model');
        if (!empty($data)) {
            $industry = $this->collection->getNewEmptyItem();
            $industry->setData($data);
            $this->loadedData[$industry->getId()] = $industry->getData();
            $this->dataPersistor->clear('current_model');
        } else {
            $industry = $this->collection->getNewEmptyItem();
            $data = $this->objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
            $this->loadedData[$industry->getId()] = $data;
        }


        return $this->loadedData;
    }

}
