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



namespace Mirasvit\Event\Event\Customer;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Mirasvit\Event\Event\Context;
use Mirasvit\Event\EventData\CustomerData;
use Mirasvit\Event\Event\CronEvent;
use Mirasvit\Event\EventData\StoreData;

class BirthdayEvent extends CronEvent
{
    const IDENTIFIER = 'customer_birthday';

    const PARAM_CUSTOMER_ID = 'customer_id';

    /**
     * @var CustomerCollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * BirthdayEvent constructor.
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param Context $context
     */
    public function __construct(
        CustomerCollectionFactory $customerCollectionFactory,
        Context $context
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return [
            self::IDENTIFIER => __('Customer / Birthday'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getEventData()
    {
        return [
            $this->context->get(CustomerData::class),
            $this->context->get(StoreData::class),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function expand($params)
    {
        $customer = $this->context->create(CustomerData::class)->load($params[CustomerData::ID]);
        $store = $this->context->create(StoreData::class)->load($params[self::PARAM_STORE_ID]);

        $params[CustomerData::IDENTIFIER] = $customer;
        $params[StoreData::IDENTIFIER] = $store;

        return $params;
    }

    /**
     * {@inheritdoc}
     */
    public function toString($params)
    {
        return __('Customer Birthday');
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Customer\Model\ResourceModel\Customer\Collection $collection */
        $collection = $this->customerCollectionFactory->create()
            ->addNameToSelect();

        $collection->getSelect()
            ->where(
                'DATE_FORMAT(dob, "%m-%d") = ?',
                date('m-d', $this->context->timeService->getFlagTimestamp(self::IDENTIFIER))
            );

        /** @var \Magento\Customer\Model\Customer $customer */
        foreach ($collection as $customer) {
            $params = [
                self::PARAM_CUSTOMER_ID    => $customer->getId(),
                self::PARAM_CUSTOMER_NAME  => $customer->getName(),
                self::PARAM_CUSTOMER_EMAIL => $customer->getEmail(),
                self::PARAM_STORE_ID       => $customer->getStoreId(),
            ];

            $this->context->eventRepository->register(
                self::IDENTIFIER,
                [$customer->getId()],
                $params
            );
        }

        $this->context->timeService->setFlagTimestamp(self::IDENTIFIER);
    }
}
