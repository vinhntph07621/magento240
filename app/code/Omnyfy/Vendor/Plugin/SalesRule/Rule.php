<?php
/**
 * Project: Multi Vendor.
 * User: jing
 * Date: 3/11/18
 * Time: 11:36 AM
 */
namespace Omnyfy\Vendor\Plugin\SalesRule;

class Rule
{
    protected $appState;

    protected $backendSession;

    public function __construct(
        \Magento\Framework\App\State $appState,
        \Magento\Backend\Model\Session $backendSession
    )
    {
        $this->appState = $appState;
        $this->backendSession = $backendSession;
    }

    public function aroundBeforeSave($subject, callable $process)
    {
        if (\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE != $this->appState->getAreaCode()) {
            return $process();
        }

        $vendorInfo = $this->backendSession->getVendorInfo();
        if (empty($vendorInfo)) {
            return $process();
        }

        $subject->setVendorId(intval($vendorInfo['vendor_id']));
    }
}