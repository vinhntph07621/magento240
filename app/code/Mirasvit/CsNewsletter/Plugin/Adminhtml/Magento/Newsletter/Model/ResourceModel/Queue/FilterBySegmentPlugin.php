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
 * @package   mirasvit/module-customer-segment
 * @version   1.0.51
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CsNewsletter\Plugin\Adminhtml\Magento\Newsletter\Model\ResourceModel\Queue;

use Magento\Newsletter\Model\Queue;
use Magento\Newsletter\Model\ResourceModel\Queue as QueueResource;
use Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory as SubscriberCollectionFactory;
use Magento\Newsletter\Model\ResourceModel\Subscriber\Collection as SubscriberCollection;
use Mirasvit\CsNewsletter\Api\Service\SubscriberFilterServiceInterface;

class FilterBySegmentPlugin
{
    /**
     * @var SubscriberFilterServiceInterface
     */
    private $subscriberFilter;
    /**
     * @var SubscriberCollectionFactory
     */
    private $subscriberCollectionFactory;

    /**
     * FilterBySegmentPlugin constructor.
     * @param SubscriberFilterServiceInterface $subscriberFilter
     * @param SubscriberCollectionFactory $subscriberCollectionFactory
     */
    public function __construct(
        SubscriberFilterServiceInterface $subscriberFilter,
        SubscriberCollectionFactory $subscriberCollectionFactory
    ) {
        $this->subscriberFilter = $subscriberFilter;
        $this->subscriberCollectionFactory = $subscriberCollectionFactory;
    }

    /**
     * Plugin hooks method in order to filter subscriber collection by segments.
     *
     * @param QueueResource $subject
     * @param Queue $queue
     * @param array $subscriberIds
     *
     * @return array - return modified array of subscriber IDs, only associated with segment
     */
    public function beforeAddSubscribersToQueue(QueueResource $subject, Queue $queue, array $subscriberIds)
    {
        $collection = $this->subscriberCollectionFactory->create();

        $collection->addFieldToFilter('subscriber_id', ['in' => $subscriberIds]);
        $this->subscriberFilter->filterBySegment($collection, $queue);

        return [$queue, $collection->getColumnValues('subscriber_id')];
    }
}
