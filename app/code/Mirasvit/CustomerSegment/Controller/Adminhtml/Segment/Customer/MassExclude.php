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
use Mirasvit\CustomerSegment\Api\Service\Segment\CustomerManagementInterface;

class MassExclude extends Action
{
    /**
     * @var CustomerManagementInterface
     */
    private $customerManagement;

    /**
     * MassExclude constructor.
     *
     * @param CustomerManagementInterface $customerManagement
     * @param Action\Context              $context
     */
    public function __construct(
        CustomerManagementInterface $customerManagement,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->customerManagement = $customerManagement;
    }


    /**
     * @inheritDoc
     */
    public function execute()
    {
        $customerIds = $this->getRequest()->getParam('segment_customer_id');
        if (!is_array($customerIds)) {
            $this->messageManager->addErrorMessage(__('Please select customer(s)'));
        } else {
            try {
                $this->customerManagement->excludeCustomersFromSegmentByIds(
                    $customerIds,
                    $this->getRequest()->getParam('segment_id')
                );

                $this->messageManager->addSuccessMessage(
                    __('Total of %1 customer(s) were excluded from this segment.', count($customerIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/segment/edit', ['id' => $this->getRequest()->getParam('segment_id')]);
    }
}