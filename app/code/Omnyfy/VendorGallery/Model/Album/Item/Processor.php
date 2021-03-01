<?php
namespace Omnyfy\VendorGallery\Model\Album\Item;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;

class Processor
{
    /**
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $fileStorageDb;

    /**
     * @var \Omnyfy\VendorGallery\Model\Album\Item\Config
     */
    protected $albumConfig;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * Processor constructor.
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb
     * @param \Omnyfy\VendorGallery\Model\Album\Item\Config $albumConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb,
        \Omnyfy\VendorGallery\Model\Album\Item\Config $albumConfig,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->fileStorageDb = $fileStorageDb;
        $this->albumConfig = $albumConfig;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    /**
     * Add image to media gallery and return new filename
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $file file path of image in file system
     * @param boolean $move if true, it will move source file
     * @param boolean $exclude mark image as disabled in product page view
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function addImage(
        \Omnyfy\VendorGallery\Model\Album $album,
        $file,
        $move = false,
        $exclude = true
    ) {
        $file = $this->mediaDirectory->getAbsolutePath($this->albumConfig->getBaseTmpMediaPath() . $file);
        if (!$this->mediaDirectory->isFile($file)) {
            throw new LocalizedException(__('The image does not exist.' . $file));
        }

        $pathinfo = pathinfo($file);
        $imgExtensions = ['jpg', 'jpeg', 'gif', 'png'];
        if (!isset($pathinfo['extension']) || !in_array(strtolower($pathinfo['extension']), $imgExtensions)) {
            throw new LocalizedException(__('Please correct the image file type.'));
        }

        $fileName = \Magento\MediaStorage\Model\File\Uploader::getCorrectFileName($pathinfo['basename']);
        $dispretionPath = \Magento\MediaStorage\Model\File\Uploader::getDispretionPath($fileName);
        $fileName = $dispretionPath . '/' . $fileName;

        $fileName = $this->getNotDuplicatedFilename($fileName, $dispretionPath);

        $destinationFile = $this->albumConfig->getMediaPath($fileName);

        try {
            /** @var $storageHelper \Magento\MediaStorage\Helper\File\Storage\Database */
            $storageHelper = $this->fileStorageDb;
            if ($move) {
                $this->mediaDirectory->renameFile($file, $destinationFile);

                //If this is used, filesystem should be configured properly
                $storageHelper->saveFile($this->albumConfig->getMediaUrl($fileName));
            } else {
                $this->mediaDirectory->copyFile($file, $destinationFile);

                $storageHelper->saveFile($this->albumConfig->getMediaUrl($fileName));
            }
        } catch (\Exception $e) {
            throw new LocalizedException(__('We couldn\'t move this file: %1.', $e->getMessage()));
        }

        $fileName = str_replace('\\', '/', $fileName);

        return $fileName;
    }

    /**
     * Get filename which is not duplicated with other files in media temporary and media directories
     *
     * @param string $fileName
     * @param string $dispretionPath
     * @return string
     */
    protected function getNotDuplicatedFilename($fileName, $dispretionPath)
    {
        $fileMediaName = $dispretionPath . '/'
            . \Magento\MediaStorage\Model\File\Uploader::getNewFileName($this->albumConfig->getMediaPath($fileName));
        $fileTmpMediaName = $dispretionPath . '/'
            . \Magento\MediaStorage\Model\File\Uploader::getNewFileName($this->albumConfig->getTmpMediaPath($fileName));

        if ($fileMediaName != $fileTmpMediaName) {
            if ($fileMediaName != $fileName) {
                return $this->getNotDuplicatedFilename(
                    $fileMediaName,
                    $dispretionPath
                );
            } elseif ($fileTmpMediaName != $fileName) {
                return $this->getNotDuplicatedFilename(
                    $fileTmpMediaName,
                    $dispretionPath
                );
            }
        }

        return $fileMediaName;
    }
}
