<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Block\Vendor;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;

/**
 * Vendor Review Tab
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Review extends Template implements IdentityInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Review resource model
     *
     * @var \Omnyfy\VendorReview\Model\ResourceModel\Review\CollectionFactory
     */
    protected $_reviewsColFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Omnyfy\VendorReview\Model\ResourceModel\Review\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Omnyfy\VendorReview\Model\ResourceModel\Review\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_reviewsColFactory = $collectionFactory;
        parent::__construct($context, $data);

        $this->setTabTitle();
    }

    /**
     * Get current vendor id
     *
     * @return null|int
     */
    public function getVendorId()
    {
        $vendor = $this->_coreRegistry->registry('vendor');
        return $vendor ? $vendor->getId() : null;
    }

    /**
     * Get URL for ajax call
     *
     * @return string
     */
    public function getVendorReviewUrl()
    {
        return $this->getUrl(
            'vendorreview/vendor/listAjax',
            [
                '_secure' => $this->getRequest()->isSecure(),
                'id' => $this->getVendorId(),
            ]
        );
    }

    /**
     * Set tab title
     *
     * @return void
     */
    public function setTabTitle()
    {
        $title = $this->getCollectionSize()
            ? __('Reviews %1', '<span class="counter">' . $this->getCollectionSize() . '</span>')
            : __('Reviews');
        $this->setTitle($title);
    }

    /**
     * Get size of reviews collection
     *
     * @return int
     */
    public function getCollectionSize()
    {
        $collection = $this->_reviewsColFactory->create()->addStoreFilter(
            $this->_storeManager->getStore()->getId()
        )->addStatusFilter(
            \Omnyfy\VendorReview\Model\Review::STATUS_APPROVED
        )->addEntityFilter(
            'vendor',
            $this->getVendorId()
        );

        return $collection->getSize();
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Omnyfy\VendorReview\Model\Review::CACHE_TAG];
    }
}
