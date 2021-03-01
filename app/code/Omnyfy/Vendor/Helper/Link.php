<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-23
 * Time: 11:34
 */
namespace Omnyfy\Vendor\Helper;

class Link extends \Magento\Framework\View\Element\Template
{
    public function CurrentPath() {
        return $this->_request->getPathInfo();
    }
}