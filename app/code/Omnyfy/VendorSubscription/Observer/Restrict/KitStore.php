<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-08-16
 * Time: 10:55
 */

namespace Omnyfy\VendorSubscription\Observer\Restrict;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\Exception\LocalizedException;
use Omnyfy\VendorSubscription\Model\Source\UsageType;

class KitStore implements \Magento\Framework\Event\ObserverInterface
{
    protected $_state;

    protected $_session;

    protected $_helper;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Omnyfy\VendorSubscription\Helper\Usage $_helper
    )
    {
        $this->_state = $state;
        $this->_helper = $_helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $eventName = $observer->getEvent()->getName();
        $page = $observer->getData('data_object');

        //if it's not in admin area, do nothing
        if (FrontNameResolver::AREA_CODE != $this->_state->getAreaCode()) {
            return;
        }

        //if it's admin, do nothing
        $vendorInfo = $this->getBackendSession()->getVendorInfo();
        if (empty($vendorInfo)) {
            return;
        }

        //omnyfy_landingpages_page_save_before, omnyfy_landingpages_page_save_after, omnyfy_landingpages_page_delete_after
        switch ($eventName) {
            case 'omnyfy_landingpages_page_save_before':
                //if it's update an existing page, do nothing
                if (!empty($page) && $page->getId()) {
                    return;
                }
                //check usage
                if ($this->_helper->isRunOut($vendorInfo['vendor_id'], UsageType::KIT_STORE)) {
                    throw new LocalizedException(__('You have used up your allocation of kit stores. Please upgrade your plan or contact your marketplace administrator for more information'));
                }
                break;
            case 'omnyfy_landingpages_page_save_after':
                //save usage log
                $this->_helper->logUsage($vendorInfo['vendor_id'], UsageType::KIT_STORE, $page->getId());
                break;
            case 'omnyfy_landingpages_page_delete_after':
                //delete usage log, update usage
                $this->_helper->returnUsageCount($vendorInfo['vendor_id'], UsageType::KIT_STORE, $page->getId());
                break;
        }
    }

    protected function getBackendSession()
    {
        if (null == $this->_session) {
            $this->_session = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Backend\Model\Session::class);
        }
        return $this->_session;
    }
}

 