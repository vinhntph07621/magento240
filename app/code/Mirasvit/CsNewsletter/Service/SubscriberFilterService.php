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




namespace Mirasvit\CsNewsletter\Service;

use Magento\Newsletter\Model\Queue;
use Magento\Newsletter\Model\ResourceModel\Subscriber\Collection as SubscriberCollection;
use Mirasvit\CsNewsletter\Api\Repository\SegmentNewsletterRepositoryInterface;
use Mirasvit\CsNewsletter\Api\Service\SubscriberFilterServiceInterface;
use Mirasvit\CustomerSegment\Api\Data\Segment\CustomerInterface;

class SubscriberFilterService implements SubscriberFilterServiceInterface
{
    /**
     * @var SegmentNewsletterRepositoryInterface
     */
    private $segmentNewsletterRepository;

    /**
     * SubscriberFilterService constructor.
     * @param SegmentNewsletterRepositoryInterface $segmentNewsletterRepository
     */
    public function __construct(SegmentNewsletterRepositoryInterface $segmentNewsletterRepository)
    {
        $this->segmentNewsletterRepository = $segmentNewsletterRepository;
    }

    /**
     * @inheritdoc
     */
    public function filterBySegment(SubscriberCollection $collection, Queue $queue)
    {
        $resource = $collection->getResource();
        $segments = $this->segmentNewsletterRepository->getByQueue($queue->getId());

        if ($segments) {
            $segments = implode(',', $segments);
            $collection->join(
                ['segment' => $resource->getTable(CustomerInterface::TABLE_NAME)],
                "(main_table.customer_id = segment.customer_id OR main_table.subscriber_email = segment.email) "
                    . "AND segment.segment_id IN ({$segments})",
                []
            );
            $collection->getSelect()->group('segment.email');
        }
    }
}
