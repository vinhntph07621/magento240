<?php
/**
 * Project: Core.
 * User: jing
 * Date: 18/5/18
 * Time: 2:16 PM
 */
namespace Omnyfy\Core\Plugin;

class ServiceOutputProcessor
{
    public function aroundConvertValue(
        \Magento\Framework\Webapi\ServiceOutputProcessor $subject,
        callable $proceed,
        $data,
        $type
    )
    {
        if (is_array($data) && ('\Omnyfy\Core\Api\Json' === $type || '\Zend\Json\Json' === $type)) {
            return $data;
        }

        return $proceed($data, $type);
    }
}