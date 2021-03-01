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



namespace Mirasvit\CustomerSegment\Controller\Adminhtml\Segment\Customer;


use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Mirasvit\CustomerSegment\Api\Data\Segment\HistoryInterface;
use Mirasvit\CustomerSegment\Api\Repository\Segment\CustomerRepositoryInterface as SegmentCustomerRepositoryInterface;
use Mirasvit\CustomerSegment\Service\Segment\History\Writer;

class MassDelete extends Action
{
    /**
     * @var SegmentCustomerRepositoryInterface
     */
    private $segmentCustomerRepository;

    /**
     * MassDelete constructor.
     *
     * @param SegmentCustomerRepositoryInterface $segmentCustomerRepository
     * @param Action\Context                     $context
     */
    public function __construct(
        SegmentCustomerRepositoryInterface $segmentCustomerRepository,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->segmentCustomerRepository = $segmentCustomerRepository;
    }


    /**
     * @inheritDoc
     */
    public function execute()
    {
        $customerIds = $this->getRequest()->getParam('segment_customer_id');
        $segmentId = $this->getRequest()->getParam('segment_id');

        if (!is_array($customerIds)) {
            $this->messageManager->addErrorMessage(__('Please select customer(s)'));
        } else {
            $count = count($customerIds);
            try {
                foreach ($customerIds as $customerId) {
                    $this->segmentCustomerRepository->deleteById($customerId);
                }

                Writer::addCustomerMessage($segmentId, $count, HistoryInterface::ACTION_REMOVE);
                $this->messageManager->addSuccessMessage(
                    __('Total of %1 record(s) were removed from this segment.', $count)
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/segment/edit', ['id' => $segmentId]);
    }
}
