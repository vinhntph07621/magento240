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
use Magento\Ui\Component\MassAction\Filter;
use Mirasvit\CustomerSegment\Api\Repository\SegmentRepositoryInterface as SegmentRepositoryInterface;

class MassDelete extends Action
{
    /**
     * @var SegmentRepositoryInterface
     */
    private $segmentRepository;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * MassDelete constructor.
     * @param SegmentRepositoryInterface $segmentRepository
     * @param Filter $filter
     * @param Action\Context $context
     */
    public function __construct(
        SegmentRepositoryInterface $segmentRepository,
        Filter $filter,
        Action\Context $context
    ) {
        $this->segmentRepository = $segmentRepository;
        $this->filter            = $filter;

        parent::__construct($context);
    }


    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        $collection     = $this->filter->getCollection($this->segmentRepository->getCollection());
        $collectionSize = $collection->getSize();

        if (!$collectionSize) {
            $this->messageManager->addErrorMessage(__('Please select segment(s)'));
        } else {
            try {
                foreach ($collection as $segment) {
                    $this->segmentRepository->delete($segment);
                }

                $this->messageManager->addSuccessMessage(
                    __('Total of %1 segment(s) were deleted.', $collectionSize)
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}
