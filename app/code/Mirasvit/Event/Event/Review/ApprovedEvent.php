<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-event
 * @version   1.2.36
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Event\Event\Review;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Review\Model\Review;
use Mirasvit\Event\Event\Context;
use Mirasvit\Event\Event\ObservableEvent;
use Mirasvit\Event\EventData\CustomerData;
use Mirasvit\Event\EventData\ProductData;
use Mirasvit\Event\EventData\ReviewData;
use Mirasvit\Event\EventData\StoreData;

class ApprovedEvent extends ObservableEvent
{
    const IDENTIFIER = 'review_approved';

    const PARAM_PRODUCT_ID = 'product_id';

    /**
     * @var ReviewCollectionFactory
     */
    private $reviewCollectionFactory;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * ApprovedEvent constructor.
     * @param CustomerFactory $customerFactory
     * @param ReviewCollectionFactory $reviewCollectionFactory
     * @param Context $context
     */
    public function __construct(
        CustomerFactory $customerFactory,
        ReviewCollectionFactory $reviewCollectionFactory,
        Context $context
    ) {
        $this->customerFactory = $customerFactory;
        $this->reviewCollectionFactory = $reviewCollectionFactory;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return [self::IDENTIFIER => __('Review / Review has been approved')];
    }

    /**
     * {@inheritdoc}
     */
    public function expand($params)
    {
        $review = $this->context->create(ReviewData::class)->load($params[ReviewData::ID]);
        $customer = $this->context->create(CustomerData::class)->load($params[CustomerData::ID]);
        $store = $this->context->create(StoreData::class)->load($params[self::PARAM_STORE_ID]);
        $product = $this->context->create(ProductInterface::class)->load($params[self::PARAM_PRODUCT_ID]);

        $params[ReviewData::IDENTIFIER] = $review;
        $params[CustomerData::IDENTIFIER] = $customer;
        $params[StoreData::IDENTIFIER] = $store;
        $params[ProductData::IDENTIFIER] = $product;

        return $params;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventData()
    {
        return [
            $this->context->get(ReviewData::class),
            $this->context->get(CustomerData::class),
            $this->context->get(StoreData::class),
            $this->context->get(ProductData::class),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toString($params)
    {
        return __('Review (ID: %1) has been approved', $params[ReviewData::ID]);
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave(Review $subject)
    {
        if ($subject->dataHasChangedFor('status_id')
            && $subject->getStatusId() == \Magento\Review\Model\Review::STATUS_APPROVED
        ) {
            $this->register($subject);
        }

        return $subject;
    }

    /**
     * Register event.
     *
     * @param Review $review
     */
    private function register(Review $review)
    {
        $stores = $review->getData('stores');
        sort($stores);
        if ($review->getData('customer_id')) {
            /** @var Customer $customer */
            $customer = $this->customerFactory->create()->load($review->getData('customer_id'));
            $customerEmail = $customer->getEmail();
            $customerName = $customer->getName();
        } else {
            $customerEmail = '';
            $customerName = $review->getData('nickname');
        }

        $params = [
            self::PARAM_STORE_ID       => end($stores),
            ReviewData::ID             => $review->getId(),
            CustomerData::ID           => $review->getData('customer_id'),
            self::PARAM_PRODUCT_ID     => $review->getEntityPkValue(),
            self::PARAM_CUSTOMER_EMAIL => $customerEmail,
            self::PARAM_CUSTOMER_NAME  => $customerName,
            self::PARAM_CREATED_AT     => $review->getData('created_at'),
        ];

        $this->context->eventRepository->register(
            self::IDENTIFIER,
            [$params[ReviewData::ID]],
            $params
        );

        $this->context->timeService->setFlagTimestamp(self::IDENTIFIER);
    }
}
