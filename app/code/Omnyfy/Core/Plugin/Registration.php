<?php
/**
 * Project: apcd.
 * User: jing
 * Date: 6/9/18
 * Time: 10:49 AM
 */
namespace Omnyfy\Core\Plugin;

class Registration
{
    const XML_PATH_ALLOW_REGISTRATION = 'customer/create_account/allow_registration';

    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_scopeConfig = $scopeConfig;
    }

    public function afterIsAllowed(\Magento\Customer\Model\Registration $subject, $result)
    {
        if (!$this->_scopeConfig->isSetFlag(self::XML_PATH_ALLOW_REGISTRATION)) {
            return false;
        }
        return $result;
    }
}