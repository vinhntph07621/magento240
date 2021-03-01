<?php


namespace Omnyfy\VendorFeatured\Controller\Adminhtml\Vendorfeatured;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeaturedTag\CollectionFactory
     */
    protected $_featuredTagCollectionFactory;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeaturedTag\CollectionFactory $featuredTagCollectionFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->_featuredTagCollectionFactory = $featuredTagCollectionFactory;
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
            $id = $this->getRequest()->getParam('vendor_featured_id');
        
            $model = $this->_objectManager->create('Omnyfy\VendorFeatured\Model\VendorFeatured')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Vendor Featured no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
        
            $model->setData($data);
        
            try {
                $model->save();

                $this->deleteVendorFeaturedtags($model->getId());
                if (is_array($data["vendor_tags"]) && count($data["vendor_tags"]) >0 ) {
                    foreach ($data["vendor_tags"] as $index => $tag) {
                        $newTag = $this->_objectManager->create('Omnyfy\VendorFeatured\Model\VendorFeaturedTag');
                        $newTag->setData(
                            ["vendor_featured_id" => $model->getId(),
                                "vendor_tag_id" => $tag]
                        );
                        $newTag->save();
                    }
                }

                $this->messageManager->addSuccessMessage(__('You saved the Vendor Featured.'));
                $this->dataPersistor->clear('omnyfy_vendorfeatured_vendor_featured');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['vendor_featured_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Vendor Featured.'));
            }
        
            $this->dataPersistor->set('omnyfy_vendorfeatured_vendor_featured', $data);
            return $resultRedirect->setPath('*/*/edit', ['vendor_featured_id' => $this->getRequest()->getParam('vendor_featured_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    public function deleteVendorFeaturedtags($vendorFeaturedId){
        try {
            /** @var \Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeaturedTag\Collection $collection */
            $collection = $this->_featuredTagCollectionFactory->create();
            $collection->addFieldToFilter('vendor_featured_id', ['eq' => $vendorFeaturedId]);

            foreach ($collection as $item) {
                $item->delete();
            }
        }catch (\Exception $exception){

        }
    }
}
