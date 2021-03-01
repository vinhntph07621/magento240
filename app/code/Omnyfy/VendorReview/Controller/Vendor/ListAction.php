<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Controller\Vendor;

use Omnyfy\VendorReview\Controller\Vendor as VendorController;
use Omnyfy\VendorReview\Model\Review;
use Magento\Catalog\Model\Vendor as CatalogVendor;
use Magento\Framework\Controller\ResultFactory;

class ListAction extends VendorController
{
    /**
     * Load specific layout handles by vendor type id
     *
     * @param CatalogVendor $vendor
     * @return \Magento\Framework\View\Result\Page
     */
    protected function getVendorPage($vendor)
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if ($vendor->getPageLayout()) {
            $resultPage->getConfig()->setPageLayout($vendor->getPageLayout());
        }
        $urlSafeSku = rawurlencode($vendor->getSku());
        $resultPage->addPageLayoutHandles(
            ['id' => $vendor->getId(), 'sku' => $urlSafeSku, 'type' => $vendor->getTypeId()]
        );
        $resultPage->addUpdate($vendor->getCustomLayoutUpdate());
        return $resultPage;
    }

    /**
     * Show list of vendor's reviews
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $vendor = $this->initVendor();
        if ($vendor) {
            $this->coreRegistry->register('vendorId', $vendor->getId());

            $settings = $this->catalogDesign->getDesignSettings($vendor);
            if ($settings->getCustomDesign()) {
                $this->catalogDesign->applyCustomDesign($settings->getCustomDesign());
            }
            $resultPage = $this->getVendorPage($vendor);
            // update breadcrumbs
            $breadcrumbsBlock = $resultPage->getLayout()->getBlock('breadcrumbs');
            if ($breadcrumbsBlock) {
                $breadcrumbsBlock->addCrumb(
                    'vendor',
                    ['label' => $vendor->getName(), 'link' => $vendor->getVendorUrl(), 'readonly' => true]
                );
                $breadcrumbsBlock->addCrumb('reviews', ['label' => __('Vendor Reviews')]);
            }
            return $resultPage;
        }
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        $resultForward->forward('noroute');
        return $resultForward;
    }
}
