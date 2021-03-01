<?php
/**
 * Project: Omnyfy Core.
 * User: jing
 * Date: 9/5/18
 * Time: 11:31 AM
 */
namespace Omnyfy\Core\Helper;

class File extends \Magento\Framework\App\Helper\AbstractHelper
{
    const WRITE_DIR_CODE = [
        \Magento\Framework\App\Filesystem\DirectoryList::MEDIA,
        \Magento\Framework\App\Filesystem\DirectoryList::TMP,
        \Magento\Framework\App\Filesystem\DirectoryList::UPLOAD,
        \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
    ];

    protected $directoryList;

    protected $io;

    protected $filesystem;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\File $io,
        \Magento\Framework\Filesystem $filesystem
    )
    {
        parent::__construct($context);
        $this->directoryList = $directoryList;
        $this->io = $io;
        $this->filesystem = $filesystem;
    }

    public function createFolder($type, $path, $permission=0770)
    {
        $this->io->mkdir(
            $this->directoryList->getPath($type) . $path,
            $permission
        );
    }

    public function isDirExist($type, $path)
    {
        $fullPath = $this->directoryList->getPath($type) . $path;
        return is_dir($fullPath);
    }

    /**
     * @param string $type  directory code
     * @param string $path  relative path
     * @return string  absolute path
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function getAbsolutePath($type, $path)
    {
        return $this->directoryList->getPath($type) . $path;
    }

    /**
     * @param $content
     * @param $fileName
     * @param $path
     * @param $type
     * @param int $permission
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function writeFile($content, $fileName, $path, $type, $permission=null)
    {
        $this->io->open(['path' => $this->getAbsolutePath($type, $path)]);
        $this->io->write($fileName, $content, $permission);
    }

    /**
     * @param string $content
     * @param string $relativeFileName
     * @param string $type directory code
     * @param int $permission permission number
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function writeFileRelative($content, $relativeFileName, $type, $permission=null)
    {
        if (!in_array($type, self::WRITE_DIR_CODE)) {
            return;
        }

        $writer = $this->filesystem->getDirectoryWrite($type);

        $writer->writeFile($relativeFileName, $content, 'w');
        if (!is_null($permission)) {
            $writer->changePermissions($relativeFileName, $permission);
        }
    }

    /**
     * @param string $content
     * @param string $relativeFileName
     * @param string $type directory code
     * @param int $permission permission
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function lockAndWriteFile($content, $relativeFileName, $type, $permission=null)
    {
        if (!in_array($type, self::WRITE_DIR_CODE)) {
            return;
        }
        $writer = $this->filesystem->getDirectoryWrite($type);
        $file = $writer->openFile($relativeFileName, 'w');
        try {
            $file->lock();
            try {
                $file->write($content);
                if (!is_null($permission)) {
                    $writer->changePermissions($relativeFileName, $permission);
                }
            }
            finally {
                $file->unlock();
            }
        }
        finally {
            $file->close();
        }
    }

    public function getMTime($relativePath, $filename, $type)
    {
        $reader = $this->filesystem->getDirectoryRead($type);
        $path = $reader->getAbsolutePath($relativePath);

        foreach(new \DirectoryIterator($path) as $file) {
            if ($file->isFile() && $filename == $file->getFilename()) {
                return $file->getMTime();
            }
        }

        return false;
    }
}