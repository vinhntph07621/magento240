<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-06-13
 * Time: 10:36
 */
namespace Omnyfy\Vendor\Model\Vendor\Attribute\Backend;

class Media extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     *
     */
    protected $_uploaderFactory;

    /**
     * Filesystem facade
     *
     * @var \Magento\Framework\Filesystem
     *
     */
    protected $_filesystem;

    /**
     * File Uploader factory
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     *
     */
    protected $_fileUploaderFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     *
     */
    protected $_logger;

    /**
     * Image uploader
     *
     * @var \Omnyfy\Vendor\Model\ImageUploader
     */
    private $imageUploader;

    /**
     * Image constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
    ) {
        $this->_filesystem = $filesystem;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_logger = $logger;
    }

    /**
     * Avoiding saving potential upload data to DB.
     * Will set empty image attribute value if image was not uploaded.
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $attributeName = $this->getAttribute()->getName();
        $value = $object->getData($attributeName);
        $imageName = $this->getUploadedImageName($value);

        if ($imageName) {
            $object->setData($attributeName, $imageName);
        } else if (!is_string($value)) {
            $object->setData($attributeName, '');
        }

        return parent::beforeSave($object);
    }

    /**
     * Gets image name from $value array.
     * Will return empty string in case $value is not an array.
     *
     * @param array $value Attribute value
     * @return string
     */
    private function getUploadedImageName($value)
    {
        if (is_array($value) && isset($value[0]['name'])) {
            return $value[0]['name'];
        }

        return '';
    }

    /**
     * Get image uploader.
     *
     * @return \Omnyfy\Vendor\Model\ImageUploader
     *
     */
    private function getImageUploader()
    {
        if ($this->imageUploader === null) {
            $this->imageUploader = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Omnyfy\Vendor\VendorImageUpload::class);
        }

        return $this->imageUploader;
    }

    /**
     * Save uploaded file and set its name to category.
     *
     * @param \Magento\Framework\DataObject $object
     * @return \Omnyfy\Vendor\Model\Vendor\Attribute\Backend\Media
     */
    public function afterSave($object)
    {
        $imageName = $object->getData($this->getAttribute()->getName(), null);
        $origImage = $object->getOrigData($this->getAttribute()->getName(), null);
        if ($imageName) {
            if ($imageName == $origImage) {
$this->_logger->debug('here1');
                return $this;
            }
            try {
                $code = $this->getAttribute()->getAttributeCode();
                $path = 'media';
                switch($code) {
                    case 'banner':
                    case 'logo':
                        $path = strtolower($code);
                        break;
                }
                $this->getImageUploader()->moveFileFromTmp($imageName, $path);
            } catch (\Exception $e) {
$this->_logger->debug('here2 exception');
                $this->_logger->critical($e);
            }
        }
        return $this;
    }
}
 
