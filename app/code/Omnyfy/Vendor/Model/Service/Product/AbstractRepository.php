<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 25/11/19
 * Time: 11:44 am
 */
namespace Omnyfy\Vendor\Model\Service\Product;

abstract class AbstractRepository
{
    protected $_logger;

    protected $_productResource;

    protected $_config;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Psr\Log\LoggerInterface $logger,
        \Omnyfy\Vendor\Model\Config $config
    ){
        $this->_productResource = $productResource;
        $this->_logger = $logger;
        $this->_config = $config;
    }

    protected function error($message)
    {
        if (!is_array($message)) {
            $message = array($message);
        }

        return [
            'error' => true,
            'message' => $message
        ];
    }

    protected function success($message)
    {
        return [
            'success' => true,
            'message' => $message
        ];
    }
}
 