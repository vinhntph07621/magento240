<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-07-29
 * Time: 08:58
 */
namespace Omnyfy\Vendor\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Store\Api\GroupRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Backend\Model\Session as BackendSession;

class Websites extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Websites
{
    protected $_session;

    public function __construct(
        LocatorInterface $locator,
        StoreManagerInterface $storeManager,
        WebsiteRepositoryInterface $websiteRepository,
        GroupRepositoryInterface $groupRepository,
        StoreRepositoryInterface $storeRepository,
        BackendSession $session
    ) {
        $this->_session = $session;
        parent::__construct($locator, $storeManager, $websiteRepository, $groupRepository, $storeRepository);
    }

    protected function getWebsitesList()
    {
        $vendorInfo = $this->_session->getVendorInfo();
        $websitesList = parent::getWebsitesList();
        if (empty($vendorInfo)) {
            return $websitesList;
        }

        $result = [];
        foreach($websitesList as $row) {
            if (in_array($row['id'], $vendorInfo['website_ids'])) {
                $result[] = $row;
            }
        }

        return $result;
    }

    protected function getWebsitesValues()
    {
        $vendorInfo = $this->_session->getVendorInfo();
        $values = parent::getWebsitesValues();
        if (empty($vendorInfo)) {
            return $values;
        }

        return $vendorInfo['website_ids'];
    }
}
 