<?php


namespace Omnyfy\Checklist\Model\Checklist;

use Magento\Framework\App\Request\DataPersistorInterface;
use Omnyfy\Checklist\Model\ResourceModel\Checklist\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    protected $collection;

    protected $dataPersistor;

    protected $loadedData;

    protected $_checklistItemscollection;

    protected $_itemOptionsCollectionFactory;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItems\CollectionFactory $checklistItemsCollectionFactory,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItemOptions\CollectionFactory $itemOptionsCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->_checklistItemscollection = $checklistItemsCollectionFactory->create();
        $this->_itemOptionsCollectionFactory = $itemOptionsCollectionFactory;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
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
        foreach ($items as $model) {
            $data = $model->getData();
            $data['data'] = ['links' => []];

            $checklist_items = $this->_checklistItemscollection;
            $checklist_items->addFieldToFilter('checklist_id', ['eq' => $data['checklist_id']]);
            $checklist_items->joinItemData();
            $items = [];

            foreach($checklist_items as $checklist_item) {
                $options = $this->_itemOptionsCollectionFactory->create();
                $options->addFieldToFilter('item_id', ['eq' => $checklist_item['checklistitems_id']]);
                $options->joinCmsArticles();

                $itemOptions = [];
                foreach($options as $option) {
                    $itemOptions[] = $option['cms_article_link'];
                }

                if ($checklist_item['is_upload'] == 1) {
                    $is_upload_enabled = false;
                } else {
                    $is_upload_enabled = true;
                }

                $items[] = [
                    'checklistitems_id' => $checklist_item['checklistitems_id'],
                    'checklist_item_title' => $checklist_item['checklist_item_title'],
                    'checklist_item_description' => $checklist_item['checklist_item_description'],
                    'enable_upload_documents' => $checklist_item['is_upload'],
                    'upload_name' => $checklist_item['upload_name'],
                    'checklistitemuploads_id' => $checklist_item['checklistitemuploads_id'],
                    'checklist_item_options' => $itemOptions,
                    'is_upload' => $checklist_item['is_upload'],
                    'is_upload_select' => $is_upload_enabled
                ];
            }
            $data['data']['links']['checklist_items'] = $items;

            $this->loadedData[$model->getId()] = $data;
        }

        $data = $this->dataPersistor->get('omnyfy_checklist_checklist');

        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('omnyfy_checklist_checklist');
        }
        return $this->loadedData;
    }
}
