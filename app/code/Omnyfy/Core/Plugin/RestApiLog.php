<?php
/**
 * Project: Core.
 * User: jing
 * Date: 31/10/18
 * Time: 12:45 PM
 */
namespace Omnyfy\Core\Plugin;

class RestApiLog
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
        $this->_logger->pushHandler(
            new \Monolog\Handler\StreamHandler(
                $this->_dir->getPath(\Magento\Framework\App\Filesystem\DirectoryList::LOG) . '/rest.log'
            )
        );
        $this->_enabled = $scopeConfig->isSetFlag(self::XML_PATH_REST_LOG);
    }

    public function beforeDispatch($subject, \Magento\Framework\App\RequestInterface $request)
    {
        if ($this->_enabled) {
            if ('OPTIONS' != $request->getMethod()) {
                $this->_logger->debug($request->getMethod() . ' ' .$request->getPathInfo(). "\n" . $request->getContent());
            }
        }
    }

    public function afterDispatch($subject, $result)
    {
        if ($this->_enabled) {
            //$this->_logger->debug($result->getContent());
        }
        return $result;
    }
}