<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-24
 * Time: 17:39
 */
namespace Omnyfy\Vendor\Controller\Adminhtml;

use Magento\Framework\Controller\ResultFactory;

abstract class AbstractUploadAction extends \Magento\Backend\App\Action
{
    protected $_fileKey;

    protected $imageUploader;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Omnyfy\Vendor\Model\ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
    }

    public function execute()
    {
        try {
            $key = $this->getRequest()->getParam('param_name', $this->_fileKey);
            $result = $this->imageUploader->saveFileToTmpDir($key);

            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}