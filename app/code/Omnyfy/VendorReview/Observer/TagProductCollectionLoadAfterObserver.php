<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Observer;

use Magento\Framework\Event\ObserverInterface;

class TagVendorCollectionLoadAfterObserver implements ObserverInterface
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
     * Add review summary info for tagged vendor collection
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        $this->_reviewFactory->create()->appendSummary($collection);

        return $this;
    }
}
