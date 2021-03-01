<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-08-06
 * Time: 16:19
 */
namespace Omnyfy\VendorSubscription\Plugin;

class HelperGetRoleId
{
    protected $_helper;

    public function __construct(
        \Omnyfy\VendorSubscription\Helper\Data $helper
    )
    {
        $this->_helper = $helper;
    }

    public function aroundGetRoleId($subject, callable $proceed, $signUp)
    {
        $result = $this->_helper->getPlanIdRoleIdBySignUp($signUp);
        if (empty($result) || !isset($result[1])) {
            return $proceed($signUp);
        }

        return $result[1];
    }
}
 