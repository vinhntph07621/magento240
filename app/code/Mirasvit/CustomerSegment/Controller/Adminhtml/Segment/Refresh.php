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



namespace Mirasvit\CustomerSegment\Controller\Adminhtml\Segment;


use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Mirasvit\CustomerSegment\Api\Data\Segment\StateInterface;
use Mirasvit\CustomerSegment\Api\Repository\SegmentRepositoryInterface;
use Mirasvit\CustomerSegment\Api\Service\Segment\RuleServiceInterface;

class Refresh extends Action
{
    /**
     * @var RuleServiceInterface|\Mirasvit\CustomerSegment\Service\Segment\AjaxRuleService
     */
    private $ruleService;

    /**
     * @var SegmentRepositoryInterface
     */
    private $segmentRepository;

    /**
     * @var Action\Context
     */
    private $context;


    /**
     * @inheritDoc
     */
    public function __construct(
        RuleServiceInterface $ruleService,
        SegmentRepositoryInterface $segmentRepository,
        Action\Context $context
    ) {
        parent::__construct($context);

        $this->ruleService       = $ruleService;
        $this->segmentRepository = $segmentRepository;
        $this->context           = $context;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $segment = $this->segmentRepository->get($this->getRequest()->getParam('id'));

        $state = $this->segmentRepository->getState($segment);
        $state->setData($this->getRequest()->getParams());

        try {
            $start = microtime(true);

            $this->ruleService->apply($segment);

            $state->setData('time', round(microtime(true) - $start, 4));
            $state->setData('success', true);
            $state->setData(StateInterface::PROGRESS, $state->getProgress());

            $resultJson->setData($state->getData());
        } catch (\Exception $e) {
            $resultJson->setData([
                'status'                 => StateInterface::STATUS_ERROR,
                'success'                => false,
                'error'                  => $e->__toString(),
                StateInterface::PROGRESS => $state->getProgress(),
            ]);
        }

        return $resultJson;
    }
}