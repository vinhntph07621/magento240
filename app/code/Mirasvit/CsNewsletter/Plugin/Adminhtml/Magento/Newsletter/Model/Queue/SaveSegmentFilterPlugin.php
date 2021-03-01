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




namespace Mirasvit\CsNewsletter\Plugin\Adminhtml\Magento\Newsletter\Model\Queue;

use Magento\Framework\App\RequestInterface;
use Magento\Newsletter\Model\Queue;
use Mirasvit\CsNewsletter\Api\Repository\SegmentNewsletterRepositoryInterface;
use Psr\Log\LoggerInterface;

class SaveSegmentFilterPlugin
{
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SegmentNewsletterRepositoryInterface
     */
    private $segmentNewsletterRepository;

    /**
     * SaveSegmentFilterPlugin constructor.
     * @param SegmentNewsletterRepositoryInterface $segmentNewsletterRepository
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     */
    public function __construct(
        SegmentNewsletterRepositoryInterface $segmentNewsletterRepository,
        RequestInterface $request,
        LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->logger = $logger;
        $this->segmentNewsletterRepository = $segmentNewsletterRepository;
    }

    /**
     * Save segment filters associated with the newsletter queue.
     *
     * @param Queue $subject
     * @param Queue $result
     *
     * @return Queue $result
     */
    public function afterSave(Queue $subject, Queue $result)
    {
        if ($result->getQueueStatus() !== Queue::STATUS_SENT) {
            if ($segments = $this->request->getParam('segments')) {
                $this->segmentNewsletterRepository->save($segments, $result->getId());
                $result->getResource()->setStores($result);
            }
        }

        return $result;
    }
}
