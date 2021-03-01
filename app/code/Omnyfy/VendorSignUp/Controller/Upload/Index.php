<?php

namespace Omnyfy\VendorSignUp\Controller\Upload;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;
    protected $jsonHelper;
    protected $_mediaDirectory;
    protected $_fileUploaderFactory;
    protected $_dir;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Filesystem\DirectoryList $dir
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_dir = $dir;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute() {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $__html = '';
        try {
            $currentDate = date('d-m-Y');
            $target = $this->_dir->getPath('pub');
            $target .= '/media/omnyfy/vendor_signup/' . $currentDate . '/';
            /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'file']);
            /** Allowed extension types */
            $uploader->setAllowedExtensions(['pdf', 'doc', 'docx', 'csv', 'jpg', 'jpeg', 'png', 'ppt', 'pptx']);
            /** rename file name if already exists */
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            $result = $uploader->save($target);

            if ($result['file']) {
                $__html .= $result['file'];
                return $resultJson->setData([
                    "message" => 'File has been successfully uploaded',
                    "type" => "success",
                    "filelist" => $__html
                ]);
            }
        } catch (\Exception $e) {
            $__message = $this->jsonResponse($e->getMessage());
            
            if ($e->getMessage() == "Disallowed file type.")
                $__message = "Disallowed file type. Please upload pdf, doc, docx, csv, jpg, jpeg, png, ppt or pptx files.";

            if (strpos($e->getMessage(), 'array is empty'))
                $__message = "Please upload pdf, doc, docx, csv, jpg, jpeg, png, ppt or pptx files.";

            return $resultJson->setData([
                        "message" => $__message,
                        "type" => "error",
                        'test' => $e->getMessage()
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
}
