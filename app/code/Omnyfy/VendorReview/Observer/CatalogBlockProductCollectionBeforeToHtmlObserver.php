<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Observer;

use Magento\Framework\Event\ObserverInterface;

class CatalogBlockVendorCollectionBeforeToHtmlObserver implements ObserverInterface
{
    /**
     * Review model
     *
     * @var \Omnyfy\VendorReview\Model\ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * @param \Omnyfy\VendorReview\Model\ReviewFactory $reviewFactory
     */
    public function __construct(
        \Omnyfy\VendorReview\Model\ReviewFactory $reviewFactory
    ) {
        $this->_reviewFactory = $reviewFactory;
    }

    /**
     * Append review summary before rendering html
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $vendorCollection = $observer->getEvent()->getCollection();
        if ($vendorCollection instanceof \Magento\Framework\Data\Collection) {
            $vendorCollection->load();
            $this->_reviewFactory->create()->appendSummary($vendorCollection);
        }

        return $this;
    }
}
