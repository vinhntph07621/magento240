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
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Api\Repository\SegmentRepositoryInterface as SegmentRepositoryInterface;
use Mirasvit\CustomerSegment\Controller\Adminhtml\SegmentAbstract;

class Compare extends SegmentAbstract
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * Compare constructor.
     * @param Filter $filter
     * @param SegmentRepositoryInterface $segmentRepository
     * @param Registry $registry
     * @param ForwardFactory $forwardFactory
     * @param Action\Context $context
     */
    public function __construct(
        Filter $filter,
        SegmentRepositoryInterface $segmentRepository,
        Registry $registry,
        ForwardFactory $forwardFactory,
        Action\Context $context
    ) {
        $this->filter = $filter;

        parent::__construct($segmentRepository, $registry, $forwardFactory, $context);
    }


    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->segmentRepository->getCollection());

        $ids = [];
        foreach ($collection as $segment) {
            $ids[] = $segment->getId();
        }

        if (count($ids) > 3) {
            $this->messageManager->addErrorMessage(__('Please select up to 3 segments'));

            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
        }

        $this->getRequest()->setParam(SegmentInterface::ID, $ids);

        /* @var $resultPage \Magento\Backend\Model\View\Result\Page */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);


        $this->initPage($resultPage)->getConfig()->getTitle()->prepend(
            __('Segments Comparison')
        );

        return $resultPage;
    }
}
