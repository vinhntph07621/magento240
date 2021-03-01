<?php
namespace Magebees\Categories\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class Downloadexportedfile extends \Magento\Backend\App\Action
{

    protected $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {

        $filename=$this->getRequest()->getParam('file');
        $filesystem = $this->_objectManager->get('Magento\Framework\Filesystem');
        $reader = $filesystem->getDirectoryRead(DirectoryList::VAR_DIR);
        $filepath = $reader->getAbsolutePath("export/".$filename);

        if (! is_file($filepath) || ! is_readable($filepath)) {
            throw new \Exception();
        }
        $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Content-type', 'application/force-download')
                ->setHeader('Content-Length', filesize($filepath))
                ->setHeader('Content-Disposition', 'attachment' . '; filename=' . basename($filepath));
        $this->getResponse()->clearBody();
        $this->getResponse()->sendHeaders();
        readfile($filepath);
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Categories::export');
    }
}
