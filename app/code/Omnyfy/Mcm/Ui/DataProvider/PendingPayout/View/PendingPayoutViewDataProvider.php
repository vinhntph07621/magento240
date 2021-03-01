<?php

namespace Omnyfy\Mcm\Ui\DataProvider\PendingPayout\View;

use Omnyfy\Mcm\Model\ResourceModel\VendorPayout\CollectionFactory;
use Omnyfy\Mcm\Model\ResourceModel\VendorPayout;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 */
class PendingPayoutViewDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider {

    /**
     * @var \Omnyfy\Mcm\Model\ResourceModel\VendorPayout\Collection
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
     * @param CollectionFactory $vendorPayoutCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
    $name, $primaryFieldName, $requestFieldName, CollectionFactory $vendorPayoutCollectionFactory, VendorPayout $vendorPayout, DataPersistorInterface $dataPersistor, array $meta = [], array $data = []
    ) {
        $this->collection = $vendorPayoutCollectionFactory->create();
        $this->vendorPayout = $vendorPayout;
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
        /** @var $vendorPayout \Omnyfy\Mcm\Model\VendorPayout */
        foreach ($items as $vendorPayout) {
            $this->loadedData[$vendorPayout->getVendorId()]['vendor_id'] = $vendorPayout->getVendorId();
        }
        return $this->loadedData;
    }

}
