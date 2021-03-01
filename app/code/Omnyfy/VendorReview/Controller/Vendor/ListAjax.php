<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Controller\Vendor;

use Magento\Framework\Exception\LocalizedException;
use Omnyfy\VendorReview\Controller\Vendor as VendorController;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Layout;

class ListAjax extends VendorController
{
    /**
     * Show list of vendor's reviews
     *
     * @return ResponseInterface|ResultInterface|Layout
     */
    public function execute()
    {
        if (!$this->initVendor()) {
            /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            return $resultForward->forward('noroute');
        }

        return $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
    }
}
