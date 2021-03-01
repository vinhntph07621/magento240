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



namespace Mirasvit\Event\EventData\Customer;

use Magento\Framework\Model\AbstractModel;
use Mirasvit\Event\Api\Data\AttributeInterface;
use Mirasvit\Event\Api\Data\Event\InstanceEventInterface;
use Mirasvit\Event\Api\Data\EventDataInterface;
use Mirasvit\Event\EventData\Condition\CustomerCondition;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Sales\Model\ResourceModel\Sale\CollectionFactory as SaleCollectionFactory;

class IsSubscriberAttribute extends CustomerAbstractAttribute implements AttributeInterface
{
    const ATTR_CODE  = 'is_subscriber';
    const ATTR_LABEL = 'Is subscriber of newsletter';
    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * IsSubscriberAttribute constructor.
     * @param SubscriberFactory $subscriberFactory
     * @param SaleCollectionFactory $saleCollectionFactory
     */
    public function __construct(
        SubscriberFactory $subscriberFactory,
        SaleCollectionFactory $saleCollectionFactory
    ) {
        $this->subscriberFactory = $subscriberFactory;

        parent::__construct($saleCollectionFactory);
    }

    /**
     * {@inheritDoc}
     */
    public function getCode()
    {
        return self::ATTR_CODE;
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return __(self::ATTR_LABEL);
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return EventDataInterface::ATTRIBUTE_TYPE_BOOL;
    }

    /**
     * Check whether customer is subscriber or not.
     *
     * {@inheritDoc}
     */
    public function getValue(AbstractModel $dataObject)
    {
        /** @var Subscriber $subscriber */
        $subscriber = $this->subscriberFactory->create();
        $subscriber->loadByEmail($dataObject->getData(InstanceEventInterface::PARAM_CUSTOMER_EMAIL));

        return $subscriber->getId() ? 1 : 0;
    }

    /**
     * {@inheritDoc}
     */
    public function getConditionClass()
    {
        return CustomerCondition::class . '|' . self::ATTR_CODE;
    }
}
