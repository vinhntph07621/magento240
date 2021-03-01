<?php


namespace Omnyfy\Checklist\Controller\Adminhtml\Checklist;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;
    protected $_checklistItems;
    protected $_checklistItemOptionsCollectionFactory;
    protected $_checklistItemUploadsCollectionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItems\CollectionFactory $checklistItemsCollectionFactory,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItemOptions\CollectionFactory $checklistItemOptionsCollectionFactory,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUploads\CollectionFactory $checklistItemUploadsCollectionFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->_checklistItems = $checklistItemsCollectionFactory;
        $this->_checklistItemOptionsCollectionFactory = $checklistItemOptionsCollectionFactory;
        $this->_checklistItemUploadsCollectionFactory = $checklistItemUploadsCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('checklist_id');
        
            $model = $this->_objectManager->create('Omnyfy\Checklist\Model\Checklist')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Checklist no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);
            try {
                $model->save();
                $checklist_id = $model->getId();
                $inputFilter = new \Zend_Filter_Input(
                    [], [], $data
                );
                $data = $inputFilter->getUnescaped();
                $tempItems   = [];
                /*Save the Check list items*/
                $checklistItemIds = $this->getChecklistItemIds($checklist_id);

                if(isset($data['data']['links']['checklist_items'])) {
                    foreach ( $data['data']['links']['checklist_items'] as $index => $item) {
                        if (isset($item['checklistitems_id'])) {
                            $tempItems[] = $item['checklistitems_id'];
                            $checklistItem = $this->_objectManager->create('Omnyfy\Checklist\Model\ChecklistItems')->load($item['checklistitems_id']);
                            $checklistItemUploads = $this->_objectManager->create('Omnyfy\Checklist\Model\ChecklistItemUploads')->load($item['checklistitemuploads_id']);
                            $checklistItem->setData($item);
                            $checklistItem->save();

                            $checklistItemUploads->setData($item);
                            $checklistItemUploads->save();

                            if (isset($item['checklist_item_options'])) {
                                //$this->_checklistItemOptionsCollectionFactory->deleteAll($item['checklistitems_id']);
                                $oldOptionsIds = $this->getChecklistOptionIds($item['checklistitems_id']);
                                $newOptionsIds = [];
                                foreach ($item['checklist_item_options'] as $article) {
                                    $checklistOptionId = $this->getChecklistOptionsId($item['checklistitems_id'],$article);
                                    if (!$checklistOptionId) {
                                        $checklistItemOptions = $this->_objectManager->create('Omnyfy\Checklist\Model\ChecklistItemOptions');
                                        $checklistItem = $this->_objectManager->create('Omnyfy\Cms\Model\Article')->load($article);
                                        $optionArray = [
                                            'item_id' => $item['checklistitems_id'],
                                            'name' => $checklistItem->getTitle(),
                                            'cms_article_link' => $article
                                        ];

                                        $checklistItemOptions->setData($optionArray);
                                        $checklistItemOptions->save();
                                    }else{
                                        $newOptionsIds[] = $checklistOptionId;
                                    }
                                }

                                $removeOptions = array_diff($oldOptionsIds, $newOptionsIds);
                                //Delete Checklist item Options
                                foreach ($removeOptions as $optionId) {;
                                    $this->_checklistItemOptionsCollectionFactory->deleteOption($optionId);
                                }
                            }
                        } else {
                            $checklistItem = $this->_objectManager->create('Omnyfy\Checklist\Model\ChecklistItems');
                            $checklistItemUploads = $this->_objectManager->create('Omnyfy\Checklist\Model\ChecklistItemUploads');
                            $checklistItemOptions = $this->_objectManager->create('Omnyfy\Checklist\Model\ChecklistItemOptions');
                            $data = [
                                'checklist_id' => $checklist_id,
                                'checklist_item_title'=> $item['checklist_item_title'] ,
                                'checklist_item_description'=> $item['checklist_item_description'] ,
                                'checklist_item_order' => $index,
                                'checklist_item_status' => 1,
                                'is_upload' => $item['is_upload']
                            ];
                            $checklistItem->setData($data);
                            $checklistItem->save();
                            $item_id = $checklistItem->getId();

                            $uploads = [
                                'item_id' => $item_id,
                                'upload_name' => $item['upload_name']
                            ];

                            $checklistItemUploads->setData($uploads);
                            $checklistItemUploads->save();

                            if (isset($item['checklist_item_options'])) {
                                foreach ($item['checklist_item_options'] as $article) {
                                    $checklistItem = $this->_objectManager->create('Omnyfy\Cms\Model\Article')->load($article);
                                    $optionArray = [
                                        'item_id' => $item_id,
                                        'name' => $checklistItem->getTitle(),
                                        'cms_article_link' => $article
                                    ];

                                    $checklistItemOptions->setData($optionArray);
                                    $checklistItemOptions->save();
                                }
                            }
                        }
                    }
                }
                $removeItems = array_diff($checklistItemIds, $tempItems);

                //Delete Checklist items
                foreach ($removeItems as $itemId) {
                    $this->_checklistItemOptionsCollectionFactory->deleteAll($itemId);
                    $this->_checklistItemUploadsCollectionFactory->deleteAll($itemId);
                    $this->_checklistItems->deleteOption($itemId);
                }

                $this->messageManager->addSuccessMessage(__('You saved the Checklist.'));
                $this->dataPersistor->clear('omnyfy_checklist_checklist');                

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['checklist_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Checklist.'.$e->getMessage()));
            }

            $this->dataPersistor->set('omnyfy_checklist_checklist', $data);
            return $resultRedirect->setPath('*/*/edit', ['checklist_id' => $this->getRequest()->getParam('checklist_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    public function getChecklistItemIds($checklist_id){
        $checklistItems = $this->_checklistItems->create();
        $checklistItems->addFilter('checklist_id', ['eq' => $checklist_id]);

        $itemIds = [];

        foreach($checklistItems as $item) {
            $itemIds[] = $item->getChecklistitemsId();
        }

        return $itemIds;
    }

    public function getChecklistOptionIds($item_id){
        $options = $this->_checklistItemOptionsCollectionFactory->create();
        $options->addFilter('item_id', ['eq' => $item_id]);

        $itemIds = [];

        foreach($options as $item) {
            $itemIds[] = $item->getChecklistitemoptionsId();
        }

        return $itemIds;
    }

    public function getChecklistOptionsId($item_id, $article_id){
        $options = $this->_checklistItemOptionsCollectionFactory->create();
        $options->addFilter('item_id', ['eq' => $item_id]);
        $options->addFilter('cms_article_link', ['eq' => $article_id]);

        if (count($options) == 1) {
            $itemIds = 0;
            foreach ($options as $item) {
                $itemIds = $item->getChecklistitemoptionsId();
            }
            return $itemIds;
        }
        return false;
    }
}
