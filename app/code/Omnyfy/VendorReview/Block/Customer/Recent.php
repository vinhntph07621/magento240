<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Block\Customer;

use Omnyfy\VendorReview\Model\ResourceModel\Review\Vendor\Collection;

/**
 * Recent Customer Reviews Block
 */
class Recent extends \Magento\Framework\View\Element\Template
{
    /**
     * Customer list template name
     *
     * @var string
     */
    protected $_template = 'Omnyfy_VendorReview::customer/list.phtml';

    /**
     * Vendor reviews collection
     *
     * @var Collection
     */
    protected $_collection;

    /**
     * Review resource model
     *
     * @var \Omnyfy\VendorReview\Model\ResourceModel\Review\Vendor\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Omnyfy\VendorReview\Model\ResourceModel\Review\Vendor\CollectionFactory $collectionFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Omnyfy\VendorReview\Model\ResourceModel\Review\Vendor\CollectionFactory $collectionFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * Truncate string
     *
     * @param string $value
     * @param int $length
     * @param string $etc
     * @param string &$remainder
     * @param bool $breakWords
     * @return string
     */
    public function truncateString($value, $length = 80, $etc = '...', &$remainder = '', $breakWords = true)
    {
        return $this->filterManager->truncate(
            $value,
            ['length' => $length, 'etc' => $etc, 'remainder' => $remainder, 'breakWords' => $breakWords]
        );
    }

    /**
     * Return collection of reviews
     *
     * @return array|\Omnyfy\VendorReview\Model\ResourceModel\Review\Vendor\Collection
     */
    public function getReviews()
    {
        if (!($customerId = $this->currentCustomer->getCustomerId())) {
            return [];
        }
        if (!$this->_collection) {
            $this->_collection = $this->_collectionFactory->create();
            $this->_collection
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->addCustomerFilter($customerId)
                ->setDateOrder()
                ->setPageSize(5)
                ->load()
                ->addReviewSummary();
        }
        return $this->_collection;
    }

    /**
     * Return review customer view url
     *
     * @return string
     */
    public function getReviewLink()
    {
        return $this->getUrl('vendorreview/customer/view/');
    }

    /**
     * Return catalog vendor view url
     *
     * @return string
     */
    public function getVendorLink()
    {
        return $this->getUrl('catalog/vendor/view/');
    }

    /**
     * Format review date
     *
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, \IntlDateFormatter::SHORT);
    }

    /**
     * Return review customer url
     *
     * @return string
     */
    public function getAllReviewsUrl()
    {
        return $this->getUrl('vendorreview/customer');
    }

    /**
     * Return review customer view url for a specific customer/review
     *
     * @param int $id
     * @return string
     */
    public function getReviewUrl($id)
    {
        return $this->getUrl('vendorreview/customer/view', ['id' => $id]);
    }
}
