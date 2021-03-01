<?php
namespace Omnyfy\VendorSignUp\Ui\DataProvider\SignUp\Form;

use Omnyfy\VendorSignUp\Model\ResourceModel\SignUp\CollectionFactory;
use Omnyfy\VendorSignUp\Model\ResourceModel\SignUp;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 */
class SignUpDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider {

    /**
     * @var \Omnyfy\VendorSignUp\Model\ResourceModel\SignUp\Collection
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
     * @param CollectionFactory $signUpCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
    $name, $primaryFieldName, $requestFieldName, CollectionFactory $signUpCollectionFactory, SignUp $signUp, DataPersistorInterface $dataPersistor, array $meta = [], array $data = []
    ) {
        $this->collection = $signUpCollectionFactory->create();
        $this->signUp = $signUp;
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
        /** @var $signUp \Omnyfy\VendorSignUp\Model\SignUp */
        foreach ($items as $signUp) {
            $this->loadedData[$signUp->getId()] = $signUp->getData();
        }

        $data = $this->dataPersistor->get('current_model');
        if (!empty($data)) {
            $signUp = $this->collection->getNewEmptyItem();
            $signUp->setData($data);
            $this->loadedData[$signUp->getId()] = $signUp->getData();
            $this->dataPersistor->clear('current_model');
        } else {
            $signUp = $this->collection->getNewEmptyItem();
            $signUp->setData($data);
            $this->loadedData[$signUp->getId()] = $signUp->getData();
        }
        return $this->loadedData;
    }
}