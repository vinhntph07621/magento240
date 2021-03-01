<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 27/07/2018
 * Time: 10:34 AM
 */

namespace Omnyfy\Checklist\Controller\Delete;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $jsonHelper;
    protected $_mediaDirectory;
    protected $_fileUploaderFactory;
    protected $_checklistItemUserUploadsFactory;
    protected $_checklistDocumentsCollectionFactory;
    protected $_date;
    protected $_timezone;
    protected $_file;
    protected $_myCustomerSession;
    protected $_checklistItemUserUploadsRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUserUploads\CollectionFactory $checklistItemUserUploadsFactory,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistDocuments\CollectionFactory $checklistDocumentCollectionFactory,
        \Omnyfy\Checklist\Model\ChecklistItemUserUploadsRepository $checklistItemUserUploadsRepository,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Filesystem\Driver\File $file
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->_mediaDirectory = $filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_myCustomerSession = $customerSession;
        $this->_checklistItemUserUploadsFactory = $checklistItemUserUploadsFactory;
        $this->_checklistDocumentsCollectionFactory = $checklistDocumentCollectionFactory;
        $this->_checklistItemUserUploadsRepository = $checklistItemUserUploadsRepository;
        $this->_date = $date;
        $this->_timezone = $timezone;
        $this->_file = $file;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $customerId= $this->getCustomerId();

        $message = "";
        $type = "success";

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        if($customerId) {
            $documentId = $this->getRequest()->getParam('documentId',false);
            if ($documentId){

                $documentCollection = $this->_checklistItemUserUploadsFactory->create();
                $documentCollection->addFilter("checklistitemuseruploads_id",["eq"=>$documentId]);
                $documentCollection->addFilter('user_id', ['eq' => $customerId]);

                if ($documentCollection->count() > 0){
                    foreach ($documentCollection as $document) {
                        $mediaRootDir = $this->_mediaDirectory->getAbsolutePath();
                        $fileName = $document->getData("upload_link");
                        try {
                            if ($this->_file->isExists($fileName)) {
                                if ($this->_file->deleteFile($fileName)) {
                                    $message = __("Your checklist has been saved");
                                }

                                if( $this->_checklistItemUserUploadsRepository->deleteById($documentId)){
                                    $message = __("Your checklist has been saved");
                                }
                            } else {
                                $message = __("Cannot Find the file.");
                                $type = "success";

                                if( $this->_checklistItemUserUploadsRepository->deleteById($documentId)){
                                    $message .= " Record was deleted from the database";
                                }
                            }
                        } catch (\Exception $exception) {
                            $message = __("Error. %1",$exception->getMessage());
                            $type = "error";
                        }
                    }

                } else {
                    $message = __("Could not delete the file");
                    $type = "error";
                }

            } else {
                $message = __("Please specific the document to delete.");
                $type = "error";
            }
        } else {
            $message = __("Your session timeout. Please login.");
            $type = "error";
        }

        return $resultJson->setData([
            "message" => $message,
            "type" => $type
        ]);
    }

    public function getCustomerId() {
        return $this->_myCustomerSession->getCustomer()->getId();
    }
}