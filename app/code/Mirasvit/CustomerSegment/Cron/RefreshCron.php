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



namespace Mirasvit\CustomerSegment\Cron;

use Magento\Framework\Shell;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Api\Repository\SegmentRepositoryInterface;
use Mirasvit\CustomerSegment\Api\Service\Segment\RuleServiceInterface;

class RefreshCron
{
    /**
     * @var RuleServiceInterface
     */
    private $ruleService;

    /**
     * @var SegmentRepositoryInterface
     */
    private $segmentRepository;

    /**
     * @var string
     */
    private $binPath;

    /**
     * @var Shell
     */
    private $shell;

    /**
     * RefreshCron constructor.
     * @param Shell $shell
     * @param RuleServiceInterface $ruleService
     * @param SegmentRepositoryInterface $segmentRepository
     */
    public function __construct(
        Shell $shell,
        RuleServiceInterface $ruleService,
        SegmentRepositoryInterface $segmentRepository
    ) {
        $this->ruleService       = $ruleService;
        $this->segmentRepository = $segmentRepository;
        $this->shell             = $shell;

        $this->binPath = PHP_BINARY . ' ' . BP . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'magento ';
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $segmentCollection = $this->segmentRepository->getCollection()
            ->addFieldToFilter(SegmentInterface::IS_MANUAL, 0)
            ->addFieldToFilter(SegmentInterface::STATUS, SegmentInterface::STATUS_ACTIVE)
            ->setOrder(SegmentInterface::UPDATED_AT, \Magento\Framework\Data\Collection::SORT_ORDER_ASC);

        /** @var SegmentInterface $segment */
        foreach ($segmentCollection as $segment) {
            $cmd = "{$this->binPath} mirasvit:customer-segment:refresh {$segment->getId()}";
            $this->shell->execute($cmd);
        }

        return true;
    }
}
