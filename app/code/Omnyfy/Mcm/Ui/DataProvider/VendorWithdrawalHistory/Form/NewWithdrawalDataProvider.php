<?php

namespace Omnyfy\Mcm\Ui\DataProvider\VendorWithdrawalHistory\Form;

use Omnyfy\Mcm\Model\ResourceModel\VendorWithdrawalHistory\CollectionFactory;
use Omnyfy\Mcm\Model\ResourceModel\VendorWithdrawalHistory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Omnyfy\Mcm\Model\VendorPayout;

/**
 * Class DataProvider
 */
class NewWithdrawalDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider {

    /**
     * @var \Omnyfy\Mcm\Model\ResourceModel\FeesCharges\Collection
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
    
    protected $payoutModel;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $withdrawalHistoryCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
    $name, $primaryFieldName, $requestFieldName, CollectionFactory $withdrawalHistoryCollectionFactory, VendorWithdrawalHistory $withdrawalHistory, DataPersistorInterface $dataPersistor, VendorPayout $payoutModel, array $meta = [], array $data = []
    ) {
        $this->collection = $withdrawalHistoryCollectionFactory->create();
        $this->withdrawalHistory = $withdrawalHistory;
        $this->dataPersistor = $dataPersistor;
        $this->payoutModel = $payoutModel;
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
        /** @var $withdrawalHistory \Omnyfy\Mcm\Model\VendorWithdrawalHistory */
        
        $data = $this->dataPersistor->get('current_model');
        if (!empty($data)) {
            $withdrawalHistory = $this->collection->getNewEmptyItem();
            $withdrawalHistory->setData($data);
            $this->loadedData[$withdrawalHistory->getId()] = $withdrawalHistory->getData();
            $this->dataPersistor->clear('current_model');
        } else {
            $withdrawalHistory = $this->collection->getNewEmptyItem();
            $withdrawalHistory->setData($data);
            
            $this->loadedData[$withdrawalHistory->getId()] = $withdrawalHistory->getData();
        }
        return $this->loadedData;
    }

}
