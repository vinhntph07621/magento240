<?php


namespace Omnyfy\Checklist\Controller\Upload;

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

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUserUploads\CollectionFactory $checklistItemUserUploadsFactory,
        \Omnyfy\Checklist\Model\ResourceModel\ChecklistDocuments\CollectionFactory $checklistDocumentCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_myCustomerSession = $customerSession;
        $this->_checklistItemUserUploadsFactory = $checklistItemUserUploadsFactory;
        $this->_checklistDocumentsCollectionFactory = $checklistDocumentCollectionFactory;
        $this->_date = $date;
        $this->_timezone = $timezone;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $current_date = $this->_timezone->date(new \DateTime($this->_date->gmtDate()))->format("Y-m-d H:i:s");
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $customerId= $this->getCustomerId();
            if($customerId) {
                $uploadid  = $this->getRequest()->getParam('uploadid',false);
                $checklistid  = $this->getRequest()->getParam('checklistid',false);
                $target = $this->_mediaDirectory->getAbsolutePath('checklistuploads/'.$customerId.'/');
                /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
                $uploader = $this->_fileUploaderFactory->create(['fileId' => 'file']);
                /** Allowed extension types */
                $uploader->setAllowedExtensions(['pdf', 'doc', 'docx', 'csv','xlsx','xls','jpg','jpeg','png', 'gif', 'txt']);
                /** rename file name if already exists */
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
                $result = $uploader->save($target);

                $__html = "";

                if ($result['file']) {
                    $file_name = $target . $result['file'];
                    $uploads = $this->_checklistItemUserUploadsFactory->create();
                    $uploads->addFilter('user_id', ['eq' => $customerId]);
                    $uploads->addFilter('item_id', ['eq' => $uploadid]);
                    $uploads->addFilter('upload_link', ['eq' => $file_name]);

                    if (count($uploads) == 0) {
                        $data = [
                            'user_id' => $customerId,
                            'item_id' => $uploadid,
                            'upload_link' => $file_name
                        ];
                        $uploadFileResult = $this->_checklistItemUserUploadsFactory->saveOptions($data);

                        $__html = '<div class="file-item">';
                        $__html.= '<a target="_blank" class="btn btn-text-green file-name" href="">'.$result['file'].'</a>';
                        $__html.= '<button class="btn btn-text-green edit delete-btn" data-uploadid="" data-document-id=""><span class="icon-trash-o"></span></button>';
                        $__html.= '</div>';
                    } else {
                        foreach($uploads as $upload){
                            $upload->setUploadLink($target . $result['file']);
                            $upload->save();
                        }
                    }

                    $isDocs = $this->_checklistDocumentsCollectionFactory->create();
                    $isDocs->addFilter('user_id', ['eq' => $customerId]);
                    $isDocs->addFilter('main_table.checklist_id', ['eq' => $checklistid]);

                    if ((count($isDocs)) <= 0 ) {
                        $data = array(
                            "user_id" => $customerId,
                            "checklist_id" => $checklistid,
                            "complete_date" => $current_date
                        );
                        $this->_checklistDocumentsCollectionFactory->saveOptions($data);
                    } else {
                        foreach($isDocs as $doc) {
                            $data = array(
                                "complete_date" => $current_date
                            );
                            $docId = $doc->getData('checklistdocument_id');
                            $this->_checklistDocumentsCollectionFactory->updateOptions($docId, $data);
                        }
                    }

                    return $resultJson->setData([
                        "message" => 'File has been successfully uploaded',
                        "type" => "success",
                        "filelist" => $__html
                    ]);
                }
            } else {
                return $resultJson->setData([
                    "message" => 'Please login to upload file',
                    "type" => "error"
                ]);
            }
        } catch (\Exception $e) {
            $__message = $this->jsonResponse($e->getMessage());
            if ($e->getMessage() == "Disallowed file type.")
                $__message = "Disallowed file type. Please upload pdf, doc, xlsx, csv, txt, jpeg, png or gif files.";

            if (strpos($e->getMessage(),'array is empty'))
                $__message = "Please upload pdf, doc, xlsx, csv, txt, jpeg, png or gif files.";

            return $resultJson->setData([
                "message" => $__message,
                "type" => "error"
            ]);
        }
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }

    public function getCustomerId() {
        return $this->_myCustomerSession->getCustomer()->getId();
    }
}
