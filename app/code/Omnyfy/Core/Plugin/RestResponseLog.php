<?php
/**
 * Project: Core.
 * User: jing
 * Date: 11/11/18
 * Time: 12:08 PM
 */
namespace Omnyfy\Core\Plugin;

class RestResponseLog
{
    const XML_PATH_REST_LOG = 'omnyfy_core/rest/log_enabled';

    protected $_dir;

    protected $_logger;

    protected $_enabled;

    public function __construct(
        \Magento\Framework\App\Filesystem\DirectoryList $dir,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_dir = $dir;
        $this->_logger = $logger;
        $this->_enabled = $scopeConfig->isSetFlag(self::XML_PATH_REST_LOG);
    }

    public function afterSendResponse($subject, $result)
    {
        if ($this->_enabled) {
            $this->_logger->debug($subject->getContent());
        }
        return $result;
    }
}