<?php
namespace Omnyfy\VendorGallery\Controller\Adminhtml\Album;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Uploader;
use \Magento\Framework\Validator\AllowedProtocols;
use \Magento\Framework\Exception\LocalizedException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RetrieveImage extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Omnyfy_VendorGallery::vendor_gallery_update';

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Omnyfy\VendorGallery\Model\Album\Item\Config
     */
    protected $albumConfig;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     * @var \Magento\Framework\Image\Adapter\AbstractAdapter
     */
    protected $imageAdapter;

    /**
     * @var \Magento\Framework\HTTP\Adapter\Curl
     */
    protected $curl;

    /**
     * @var \Magento\MediaStorage\Model\ResourceModel\File\Storage\File
     */
    protected $fileUtility;

    /**
     * URI validator
     *
     * @var AllowedProtocols
     */
    private $validator;

    /**
     * @var \Magento\MediaStorage\Model\File\Validator\NotProtectedExtension
     */
    private $extensionValidator;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $fileDriver;

    /**
     * RetrieveImage constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Omnyfy\VendorGallery\Model\Album\Item\Config $albumConfig
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param \Magento\Framework\Image\AdapterFactory $imageAdapterFactory
     * @param \Magento\Framework\HTTP\Adapter\Curl $curl
     * @param \Magento\MediaStorage\Model\ResourceModel\File\Storage\File $fileUtility
     * @param \Magento\MediaStorage\Model\File\Validator\NotProtectedExtension $extensionValidator
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Omnyfy\VendorGallery\Model\Album\Item\Config $albumConfig,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\Image\AdapterFactory $imageAdapterFactory,
        \Magento\Framework\HTTP\Adapter\Curl $curl,
        \Magento\MediaStorage\Model\ResourceModel\File\Storage\File $fileUtility,
        \Magento\MediaStorage\Model\File\Validator\NotProtectedExtension $extensionValidator,
        \Magento\Framework\Filesystem\Driver\File $fileDriver
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->albumConfig = $albumConfig;
        $this->fileSystem = $fileSystem;
        $this->imageAdapter = $imageAdapterFactory->create();
        $this->curl = $curl;
        $this->fileUtility = $fileUtility;
        $this->extensionValidator = $extensionValidator;
        $this->fileDriver = $fileDriver;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Raw|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        $baseTmpMediaPath = $this->albumConfig->getBaseTmpMediaPath();
        try {
            if (!$this->getRequest()->isPost()) {
                throw new LocalizedException(__('Invalid request type.'));
            }
            $remoteFileUrl = $this->getRequest()->getParam('remote_image');
            $this->validateRemoteFile($remoteFileUrl);
            $originalFileName = basename($remoteFileUrl);
            $localFileName = Uploader::getCorrectFileName($originalFileName);
            $localTmpFileName = Uploader::getDispretionPath($localFileName) . DIRECTORY_SEPARATOR . $localFileName;
            $localFileMediaPath = $baseTmpMediaPath . ($localTmpFileName);
            $localUniqueFileMediaPath = $this->appendNewFileName($localFileMediaPath);
            $this->validateRemoteFileExtensions($localUniqueFileMediaPath);
            $this->retrieveRemoteImage($remoteFileUrl, $localUniqueFileMediaPath);
            $localFileFullPath = $this->appendAbsoluteFileSystemPath($localUniqueFileMediaPath);
            $this->imageAdapter->validateUploadFile($localFileFullPath);
            $result = $this->appendResultSaveRemoteImage($localUniqueFileMediaPath);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
            if (isset($localFileFullPath) && $this->fileDriver->isExists($localFileFullPath)) {
                $this->fileDriver->deleteFile($localFileFullPath);
            }
        }

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));
        return $response;
    }

    /**
     * Get URI validator
     *
     * @return AllowedProtocols
     */
    private function getValidator()
    {
        if ($this->validator === null) {
            $this->validator = $this->_objectManager->get(AllowedProtocols::class);
        }

        return $this->validator;
    }

    /**
     * Validate remote file
     *
     * @param string $remoteFileUrl
     * @throws LocalizedException
     *
     * @return $this
     */
    private function validateRemoteFile($remoteFileUrl)
    {
        /** @var AllowedProtocols $validator */
        $validator = $this->getValidator();
        if (!$validator->isValid($remoteFileUrl)) {
            throw new LocalizedException(
                __("Protocol isn't allowed")
            );
        }

        return $this;
    }

    /**
     * Invalidates files that have script extensions.
     *
     * @param string $filePath
     * @throws \Magento\Framework\Exception\ValidatorException
     * @return void
     */
    private function validateRemoteFileExtensions($filePath)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        if (!$this->extensionValidator->isValid($extension)) {
            throw new \Magento\Framework\Exception\ValidatorException(__('Disallowed file type.'));
        }
    }

    /**
     * @param string $fileName
     * @return mixed
     */
    protected function appendResultSaveRemoteImage($fileName)
    {
        $fileInfo = pathinfo($fileName);
        $tmpFileName = Uploader::getDispretionPath($fileInfo['basename']) . DIRECTORY_SEPARATOR . $fileInfo['basename'];
        $result['name'] = $fileInfo['basename'];
        $result['type'] = $this->imageAdapter->getMimeType();
        $result['error'] = 0;
        $result['size'] = filesize($this->appendAbsoluteFileSystemPath($fileName));
        $result['url'] = $this->albumConfig->getTmpMediaUrl($tmpFileName);
        $result['file'] = $tmpFileName;
        return $result;
    }

    /**
     * @param string $fileUrl
     * @param string $localFilePath
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function retrieveRemoteImage($fileUrl, $localFilePath)
    {
        $this->curl->setConfig(['header' => false]);
        $this->curl->write('GET', $fileUrl);
        $image = $this->curl->read();
        if (empty($image)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Could not get preview image information. Please check your connection and try again.')
            );
        }
        $this->fileUtility->saveFile($localFilePath, $image);
    }

    /**
     * @param string $localFilePath
     * @return string
     */
    protected function appendNewFileName($localFilePath)
    {
        $destinationFile = $this->appendAbsoluteFileSystemPath($localFilePath);
        $fileName = Uploader::getNewFileName($destinationFile);
        $fileInfo = pathinfo($localFilePath);
        return $fileInfo['dirname'] . DIRECTORY_SEPARATOR . $fileName;
    }

    /**
     * @param string $localTmpFile
     * @return string
     */
    protected function appendAbsoluteFileSystemPath($localTmpFile)
    {
        /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
        $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
        $pathToSave = $mediaDirectory->getAbsolutePath();
        return $pathToSave . $localTmpFile;
    }
}
