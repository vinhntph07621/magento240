<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 3/22/2018
 * Time: 4:16 PM
 */

namespace Omnyfy\Checklist\Controller\Download;


class Index
{
    public function execute()
    {
        return $this->_fileFactory->create(
            $filename,
            [
                'type' => "filename",
                'value' => "folder/{$filename}",
                'rm' => false,
            ],
            \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
        );
    }
}