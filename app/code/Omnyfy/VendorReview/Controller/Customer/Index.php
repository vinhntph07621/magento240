<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Controller\Customer;

use Omnyfy\VendorReview\Controller\Customer as CustomerController;
use Magento\Framework\Controller\ResultFactory;

class Index extends CustomerController
{
    /**
     * Render my vendor reviews
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if ($navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('vendorreview/customer');
        }
        if ($block = $resultPage->getLayout()->getBlock('review_customer_list')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $resultPage->getConfig()->getTitle()->set(__('My Vendor Reviews'));
        return $resultPage;
    }
}
