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




namespace Mirasvit\CsNewsletter\Plugin\Adminhtml\Magento\Newsletter\Controller\Adminhtml\Queue\Save;

use Magento\Framework\App\RequestInterface;
use Magento\Newsletter\Controller\Adminhtml\Queue\Save;
use Mirasvit\CsNewsletter\Api\Repository\SegmentNewsletterRepositoryInterface;
use Psr\Log\LoggerInterface;

class RemoveSegmentFilterPlugin
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
     * RemoveSegmentFilterPlugin constructor.
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
     * Remove segment filter associated with the newsletter queue.
     *
     * @param Save $subject
     */
    public function beforeExecute(Save $subject)
    {
        try {
            $queueId = $this->request->getParam('id');
            if ($queueId) {
                $this->segmentNewsletterRepository->deleteByQueue($queueId);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }
}
