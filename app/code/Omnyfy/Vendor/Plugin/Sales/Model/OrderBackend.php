<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-02-27
 * Time: 16:06
 */

namespace Omnyfy\Vendor\Plugin\Sales\Model;


class OrderBackend
{
    protected $_helper;

    public function __construct(\Omnyfy\Vendor\Helper\Backend $helper)
    {
        $this->_helper = $helper;
    }

    public function aroundCanInvoice($subject, callable $process)
    {
        if ($this->_helper->getBackendVendorId() == 0) {
            return $process();
        }
        return false;
    }
}