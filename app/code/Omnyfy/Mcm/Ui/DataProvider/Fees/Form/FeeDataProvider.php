<?php

namespace Omnyfy\Mcm\Ui\DataProvider\Fees\Form;

use Omnyfy\Mcm\Model\ResourceModel\FeesCharges\CollectionFactory;
use Omnyfy\Mcm\Model\ResourceModel\FeesCharges;
use Magento\Framework\App\Request\DataPersistorInterface;
use Omnyfy\Mcm\Model\VendorPayout;

/**
 * Class DataProvider
 */
class FeeDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider {

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
     * @param CollectionFactory $feeCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
    $name, $primaryFieldName, $requestFieldName, CollectionFactory $feesCollectionFactory, FeesCharges $feesCharges, DataPersistorInterface $dataPersistor, VendorPayout $payoutModel, array $meta = [], array $data = []
    ) {
        $this->collection = $feesCollectionFactory->create();
        $this->feesCharges = $feesCharges;
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
        /** @var $fee \Omnyfy\Mcm\Model\FeesCharges */
        foreach ($items as $fee) {
            $feeData = $fee->getData();
            $payoutModel = $this->payoutModel->load($fee->getId(), 'fees_charges_id');            
            $feeData['ewallet_id'] = $payoutModel->getEwalletId();
            $this->loadedData[$fee->getId()] = $feeData;
        }

        $data = $this->dataPersistor->get('current_model');
        if (!empty($data)) {
            $fee = $this->collection->getNewEmptyItem();
            $fee->setData($data);
            $this->loadedData[$fee->getId()] = $fee->getData();
            $this->dataPersistor->clear('current_model');
        } else {
            $fee = $this->collection->getNewEmptyItem();
            $fee->setData($data);
            
            $this->loadedData[$fee->getId()] = $fee->getData();
        }
        //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($this->loadedData, true));
        return $this->loadedData;
    }

}
