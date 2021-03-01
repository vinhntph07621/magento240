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



namespace Mirasvit\CsNewsletter\Plugin\Crontab\Magento\Newsletter\Model\ResourceModel\Subscriber\Collection;

use Magento\Newsletter\Model\Queue;
use Magento\Newsletter\Model\ResourceModel\Subscriber\Collection as SubscriberCollection;
use Mirasvit\CsNewsletter\Api\Service\SubscriberFilterServiceInterface;

class FilterBySegmentPlugin
{
    /**
     * @var SubscriberFilterServiceInterface
     */
    private $subscriberFilter;

    /**
     * FilterBySegmentPlugin constructor.
     * @param SubscriberFilterServiceInterface $subscriberFilter
     */
    public function __construct(SubscriberFilterServiceInterface $subscriberFilter)
    {
        $this->subscriberFilter = $subscriberFilter;
    }

    /**
     * Plugin hooks method in order to filter subscriber collection by segments.
     *
     * @param SubscriberCollection $subject
     * @param Queue                $queue
     */
    public function beforeUseQueue(SubscriberCollection $subject, Queue $queue)
    {
        $this->subscriberFilter->filterBySegment($subject, $queue);
    }
}
